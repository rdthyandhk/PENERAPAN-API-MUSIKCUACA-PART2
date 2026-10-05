const musicBtn = document.getElementById('musicBtn');
const musicQuery = document.getElementById('musicQuery');
const musicResults = document.getElementById('musicResults');
const musicStatus = document.getElementById('musicStatus');

function escapeHtml(value = '') {
    return String(value).replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    } [char]));
}

async function searchMusic() {
    const q = musicQuery.value.trim();
    if (!q) return;

    musicStatus.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Mencari musik...';
    musicResults.innerHTML = '';

    try {
        const res = await fetch(`api/music.php?q=${encodeURIComponent(q)}`);
        const json = await res.json();

        if (!json.success) throw new Error(json.message);

        musicStatus.textContent = `${json.total.toLocaleString('id-ID')} hasil ditemukan`;
        musicResults.innerHTML = json.data.map(track => `
            <div class="col-md-6 col-xl-4">
                <div class="music-card h-100">
                    <img src="${escapeHtml(track.album?.cover_medium || track.album?.cover || '')}" alt="">
                    <div class="p-3">
                        <div class="small text-primary mb-1">${escapeHtml(track.artist?.name || 'Unknown')}</div>
                        <h5>${escapeHtml(track.title || 'Unknown')}</h5>
                        <p class="small text-secondary mb-3">${escapeHtml(track.album?.title || '')}</p>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <a class="btn btn-sm btn-primary" href="${escapeHtml(track.link || '#')}" target="_blank" rel="noopener noreferrer">Buka iTunes</a>
                            <button class="btn btn-sm btn-favorite" data-track='${JSON.stringify(track).replace(/'/g, "&#39;")}'>
                                <i class="bi bi-heart"></i> Favorit
                            </button>
                            ${track.preview ? `<audio controls preload="none" src="${escapeHtml(track.preview)}"></audio>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        bindFavoriteButtons();
    } catch (err) {
        musicStatus.innerHTML = `<span class="text-danger">${escapeHtml(err.message)}</span>`;
    }
}

musicBtn.addEventListener('click', searchMusic);
musicQuery.addEventListener('keydown', e => {
    if (e.key === 'Enter') searchMusic();
});


async function getFavorites() {
    const status = document.getElementById('favoriteStatus');
    const container = document.getElementById('favoriteResults');
    if (!container) return;

    try {
        const res = await fetch('api/favorite.php?action=list');
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        if (!json.data.length) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-favorite">
                        <i class="bi bi-heart"></i>
                        <h5>Belum ada lagu favorit</h5>
                        <p>Pilih tombol <b>Favorit</b> pada lagu yang kamu suka.</p>
                    </div>
                </div>`;
            status.textContent = '';
            return;
        }

        status.textContent = `${json.data.length} lagu tersimpan`;
        container.innerHTML = json.data.map(track => `
            <div class="col-md-6 col-xl-4">
                <div class="music-card favorite-card h-100">
                    <img src="${escapeHtml(track.cover || '')}" alt="">
                    <div class="p-3">
                        <div class="small text-primary mb-1">${escapeHtml(track.artist)}</div>
                        <h5>${escapeHtml(track.title)}</h5>
                        <p class="small text-secondary">${escapeHtml(track.album || '')}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-sm btn-primary" href="${escapeHtml(track.deezer_link || '#')}" target="_blank" rel="noopener noreferrer">Buka lagu</a>
                            <button class="btn btn-sm btn-outline-danger remove-favorite" data-id="${track.deezer_id}">
                                <i class="bi bi-heart-fill"></i> Hapus
                            </button>
                            ${track.preview ? `<audio controls preload="none" src="${escapeHtml(track.preview)}"></audio>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('.remove-favorite').forEach(btn => {
            btn.addEventListener('click', async () => {
                const form = new FormData();
                form.append('action', 'remove');
                form.append('deezer_id', btn.dataset.id);
                await fetch('api/favorite.php', {
                    method: 'POST',
                    body: form
                });
                getFavorites();
                searchMusic();
            });
        });
    } catch (err) {
        status.textContent = err.message;
    }
}

async function toggleFavorite(track, button) {
    const form = new FormData();
    form.append('action', 'toggle');
    form.append('deezer_id', track.id);
    form.append('title', track.title || '');
    form.append('artist', track.artist?.name || '');
    form.append('album', track.album?.title || '');
    form.append('cover', track.album?.cover_medium || track.album?.cover || '');
    form.append('preview', track.preview || '');
    form.append('link', track.link || '');

    try {
        const res = await fetch('api/favorite.php', {
            method: 'POST',
            body: form
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        button.classList.toggle('active', json.favorited);
        button.innerHTML = json.favorited ?
            '<i class="bi bi-heart-fill"></i> Favorit' :
            '<i class="bi bi-heart"></i> Favorit';

        getFavorites();
    } catch (err) {
        alert(err.message);
    }
}

function bindFavoriteButtons() {
    document.querySelectorAll('.btn-favorite').forEach(button => {
        button.addEventListener('click', () => {
            try {
                const track = JSON.parse(button.dataset.track.replace(/&#39;/g, "'"));
                toggleFavorite(track, button);
            } catch (e) {
                console.error(e);
            }
        });
    });
}

const weatherBtn = document.getElementById('weatherBtn');
const cityInput = document.getElementById('cityInput');
const weatherResult = document.getElementById('weatherResult');

const weatherIcons = {
    'Cerah': '☀️',
    'Berawan': '⛅',
    'Berkabut': '🌫️',
    'Gerimis': '🌦️',
    'Hujan': '🌧️',
    'Salju': '❄️',
    'Badai': '⛈️'
};

async function loadWeather(city = cityInput.value.trim()) {
    if (!city) return;
    weatherResult.innerHTML = '<div class="text-center"><div class="spinner-border"></div><p class="mt-3">Mengambil data cuaca...</p></div>';

    try {
        const res = await fetch(`api/weather.php?city=${encodeURIComponent(city)}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        const c = json.current;
        weatherResult.innerHTML = `
            <div class="weather-top">
                <div>
                    <small class="text-secondary">Cuaca saat ini</small>
                    <h3>${escapeHtml(json.location.name)}, ${escapeHtml(json.location.country)}</h3>
                </div>
                <div class="weather-icon">${weatherIcons[c.description] || '🌤️'}</div>
            </div>
            <div class="temperature">${Math.round(c.temperature)}°C</div>
            <h5>${escapeHtml(c.description)}</h5>
            <div class="weather-stats">
                <div><i class="bi bi-droplet"></i><span>Kelembapan</span><strong>${c.humidity}%</strong></div>
                <div><i class="bi bi-thermometer-half"></i><span>Terasa</span><strong>${Math.round(c.apparent)}°C</strong></div>
                <div><i class="bi bi-wind"></i><span>Angin</span><strong>${c.wind} km/h</strong></div>
            </div>
            <small class="text-secondary">Update: ${escapeHtml(c.time.replace('T', ' '))}</small>
        `;
    } catch (err) {
        weatherResult.innerHTML = `<div class="text-center text-danger">${escapeHtml(err.message)}</div>`;
    }
}

weatherBtn.addEventListener('click', () => loadWeather());
cityInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') loadWeather();
});
document.querySelectorAll('.quick-cities button').forEach(btn => {
    btn.addEventListener('click', () => {
        cityInput.value = btn.dataset.city;
        loadWeather(btn.dataset.city);
    });
});

window.addEventListener('DOMContentLoaded', () => {
    searchMusic();
    loadWeather('Jakarta');
    getFavorites();
});