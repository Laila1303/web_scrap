import './bootstrap';

// Default fallback tracks list
const DEFAULT_TRACKS = [
    {
        title: "Happy Birthday, Kayla 🎂",
        artist: "From Langit, Awan, Salju &hearts;",
        url: "https://assets.codepen.io/4358584/Anitek_-_01_-_Kisses.mp3"
    },
    {
        title: "Nostalgic Afternoon 🍂",
        artist: "Lofi Friendship Vibe",
        url: "https://assets.codepen.io/4358584/Anitek_-_02_-_Kisses_Instrumental.mp3"
    },
    {
        title: "Linen Pages & pressed Flowers 🌸",
        artist: "Scrapbook Melancholy",
        url: "https://assets.codepen.io/4358584/Anitek_-_03_-_Our_Lounge.mp3"
    }
];

// Global Persistent Audio Element
if (!window.bgAudio) {
    window.bgAudio = document.createElement('audio');
    window.bgAudio.id = 'bg-audio';
    document.body.appendChild(window.bgAudio);
}
const audio = window.bgAudio;

// Core background music auto-resume logic
function initBackgroundMusic() {
    let tracks = DEFAULT_TRACKS;
    try {
        const stored = localStorage.getItem('playlist_tracks');
        if (stored) {
            tracks = JSON.parse(stored);
        }
    } catch (e) {}

    let currentTrackIdx = parseInt(localStorage.getItem('current_track_idx') || '0');
    if (currentTrackIdx >= tracks.length) currentTrackIdx = 0;

    const track = tracks[currentTrackIdx];
    if (track) {
        let currentPath = '';
        try {
            if (audio.src) {
                currentPath = decodeURIComponent(new URL(audio.src).pathname);
            }
        } catch (err) {}
        
        let targetPath = '';
        try {
            targetPath = decodeURIComponent(new URL(track.url, window.location.origin).pathname);
        } catch (err) {}

        if (!audio.src || currentPath !== targetPath) {
            audio.src = track.url;
            audio.load();
        }
    }

    // Auto-resume if it was playing before page reload/redirect
    const wasPlaying = localStorage.getItem('audio_playing') === 'true';
    if (wasPlaying && audio.paused) {
        const savedTime = parseFloat(localStorage.getItem('audio_current_time') || '0');
        if (savedTime > 0) {
            audio.currentTime = savedTime;
        }
        audio.play().catch((err) => {
            console.warn("Autoplay blocked on fresh page load, waiting for user interaction.", err);
        });
    }

    // Periodically save state
    audio.addEventListener('timeupdate', () => {
        if (audio.currentTime > 0) {
            localStorage.setItem('audio_current_time', audio.currentTime);
        }
    });

    audio.addEventListener('play', () => {
        localStorage.setItem('audio_playing', 'true');
    });

    audio.addEventListener('pause', () => {
        localStorage.setItem('audio_playing', 'false');
    });

    audio.addEventListener('ended', () => {
        let nextIdx = (currentTrackIdx + 1) % tracks.length;
        localStorage.setItem('current_track_idx', nextIdx);
        window.dispatchEvent(new CustomEvent('track-ended'));
        
        const nextTrack = tracks[nextIdx];
        if (nextTrack) {
            audio.src = nextTrack.url;
            audio.load();
            audio.play().catch(e => console.warn(e));
        }
    });
}

// Start player background logic on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBackgroundMusic);
} else {
    initBackgroundMusic();
}

// PJAX Page transition script for continuous music playback
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href) {
        const hrefAttr = link.getAttribute('href');
        if (hrefAttr && !hrefAttr.startsWith('#') && link.href.startsWith(window.location.origin) && !link.hasAttribute('download')) {
        // Exclude direct file uploads/downloads or special routes
        if (link.href.includes('/download') || link.href.includes('/export')) return;
        
        e.preventDefault();
        const url = link.href;
        
        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Identify dynamic content wrappers (standard class or id)
                const newContent = doc.querySelector('.max-w-6xl') || doc.querySelector('.max-w-4xl');
                const oldContent = document.querySelector('.max-w-6xl') || document.querySelector('.max-w-4xl');
                
                if (newContent && oldContent) {
                    oldContent.innerHTML = newContent.innerHTML;
                    document.title = doc.title;
                    history.pushState({ pjax: true }, '', url);
                    
                    // Re-execute scripts within the updated DOM container
                    const scripts = oldContent.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    
                    // Dispatch load events for newly pasted page JS
                    window.dispatchEvent(new Event('DOMContentLoaded'));
                    window.dispatchEvent(new Event('load'));
                } else {
                    window.location.href = url;
                }
            })
            .catch(() => {
                window.location.href = url;
            });
        }
    }
});

// Full reload if using browser back/forward to ensure absolute consistency
window.addEventListener('popstate', (e) => {
    window.location.reload();
});
