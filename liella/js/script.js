


// NAVBAR SHRINK ON SCROLL + LOGO ANIMATION
const navbar = document.getElementById('mainNavbar');
const navLogo = document.getElementById('navLogo');

let lastScrollY = 0;
const shrinkThreshold = 80;

function updateNavbar() {
    const y = window.scrollY || window.pageYOffset;

    if (y > shrinkThreshold && !navbar.classList.contains('navbar-shrunk')) {
        navbar.classList.remove('navbar-expanded');
        navbar.classList.add('navbar-shrunk');

        // fade-out then smaller fade-in: handled by CSS transitions
        navLogo.style.opacity = '0';
        setTimeout(() => {
            navLogo.src = 'assets/Liella!_Official_Logo_White.png';
            navLogo.style.opacity = '1';
        }, 120);

    } else if (y <= shrinkThreshold && !navbar.classList.contains('navbar-expanded')) {
        navbar.classList.remove('navbar-shrunk');
        navbar.classList.add('navbar-expanded');

        navLogo.style.opacity = '0';
        setTimeout(() => {
            navLogo.src = 'assets/Liella!_Official_Logo.png';
            navLogo.style.opacity = '1';
        }, 180);
    }

    lastScrollY = y;
}

window.addEventListener('scroll', updateNavbar);
window.addEventListener('load', updateNavbar);

// HERO SLIDESHOW (simple fade)
const slides = document.querySelectorAll('.hero-slide');
let currentSlide = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
    });
}

function nextSlide() {
    if (!slides.length) return;
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

if (slides.length > 1) {
    setInterval(nextSlide, 6000); // change every 6 seconds
}

// SCROLL REVEAL USING INTERSECTION OBSERVER
const revealEls = document.querySelectorAll('.reveal, .hero-overlay');


const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    },
    {
        threshold: 0.15
    }
);

revealEls.forEach((el, index) => {
    // add simple staggering based on index
    if (index % 3 === 1) el.classList.add('stagger-1');
    if (index % 3 === 2) el.classList.add('stagger-2');
    observer.observe(el);
});

// SEARCH BAR TOGGLE
const searchToggle = document.querySelector('.search-toggle');
const searchBar = document.querySelector('.nav-search-expanded');

if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', () => {
        searchBar.classList.toggle('active');

        // Focus input when opened
        if (searchBar.classList.contains('active')) {
            setTimeout(() => searchBar.querySelector('input').focus(), 50);
        }
    });

    // Close when clicking outside
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.nav-search-wrapper')) {
            searchBar.classList.remove('active');
        }
    });
}
// Inline Search — fixed
const searchWrap = document.querySelector(".nav-search-inline");
const searchTrigger = document.querySelector(".search-trigger");
const inlineBar = document.querySelector(".search-bar");
const inlineInput = document.querySelector(".search-bar input");

if (searchTrigger && inlineBar) {
    searchTrigger.addEventListener("click", () => {
        searchWrap.classList.add("active");

        setTimeout(() => inlineInput.focus(), 100);
    });

    document.addEventListener("click", (e) => {
        if (!searchWrap.contains(e.target)) {
            searchWrap.classList.remove("active");
        }
    });
}

// ======================
// EVENTS PAGE MODAL + AJAX
// ======================

document.addEventListener('DOMContentLoaded', () => {
    const modalOverlay = document.getElementById('eventModal');
    if (!modalOverlay) return; // only on events page

    const modalClose = modalOverlay.querySelector('.event-modal-close');
    const modalEventName = document.getElementById('modalEventName');
    const modalEventDate = document.getElementById('modalEventDate');
    const modalEventVenue = document.getElementById('modalEventVenue');
    const modalEventDescription = document.getElementById('modalEventDescription');
    const modalEventId = document.getElementById('modalEventId');
    const registerForm = document.getElementById('eventRegisterForm');
    const modalMessage = document.getElementById('modalMessage');
    const modalSuccess = document.getElementById('modalSuccess');

    // open modal when clicking event item
    document.querySelectorAll('.event-item-full').forEach(item => {
        item.addEventListener('click', () => {
            const name = item.dataset.eventName || '';
            const date = item.dataset.eventDate || '';
            const venue = item.dataset.eventVenue || '';
            const desc = item.dataset.eventDescription || '';
            const id   = item.dataset.eventId || '';

            if (modalEventName) modalEventName.textContent = name;
            if (modalEventDate) modalEventDate.textContent = date;
            if (modalEventVenue) {
                modalEventVenue.textContent = venue ? `Venue: ${venue}` : '';
            }
            if (modalEventDescription) modalEventDescription.textContent = desc;
            if (modalEventId) modalEventId.value = id;

            if (modalMessage) modalMessage.textContent = '';
            if (modalSuccess) modalSuccess.style.display = 'none';

            modalOverlay.classList.add('active');
        });
    });

    // close modal
    if (modalClose) {
        modalClose.addEventListener('click', () => {
            modalOverlay.classList.remove('active');
        });
    }
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay || e.target.classList.contains('event-modal-backdrop')) {
            modalOverlay.classList.remove('active');
        }
    });

    // if not logged in, form doesn't exist (we show login message instead)
    if (!registerForm) return;

    // handle registration via AJAX
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!IS_LOGGED_IN) {
            window.location.href = 'login.php';
            return;
        }

        const formData = new FormData(registerForm);

        fetch('register_event.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.requireLogin && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (modalMessage) {
                    modalMessage.textContent = data.message || '';
                }

                if (data.success) {
                    if (modalSuccess) {
                        modalSuccess.style.display = 'block';
                    }
                } else {
                    if (modalSuccess) {
                        modalSuccess.style.display = 'none';
                    }
                }
            })
            .catch(() => {
                if (modalMessage) {
                    modalMessage.textContent = 'Something went wrong. Please try again.';
                }
            });
    });
});

/* ==================================
   NAVBAR FIRST LOAD ENTRANCE
   ================================== */

window.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("mainNavbar");
    if (!navbar) return;

    // Add initial animation class
    navbar.classList.add("navbar-animate");

    // Delay applying the "show" state for smooth entrance
    setTimeout(() => {
        navbar.classList.add("navbar-show");
    }, 50); 
});

document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll("a");
    const frame = document.getElementById("transition-frame");

    /* ---------------------------
       1. ENTRY ANIMATION (new page)
       --------------------------- */
    if (sessionStorage.getItem("liella-transition")) {
        // Clear flag so it plays ONLY once
        sessionStorage.removeItem("liella-transition");

        // Start entry animation
        frame.classList.add("active-entry");

        // Remove after animation ends
        setTimeout(() => {
            frame.classList.remove("active-entry");
        }, 900);
    }


    /* ---------------------------
       2. EXIT ANIMATION (on link click)
       --------------------------- */
    links.forEach(link => {
        const href = link.getAttribute("href");

        // Only animate internal links
        if (
            href && 
            !href.startsWith("#") &&
            !href.startsWith("http")
            
            ) {
            link.addEventListener("click", e => {
                e.preventDefault();

                // Mark that next page must play entry animation
                sessionStorage.setItem("liella-transition", "1");

                // Start exit animation
                frame.classList.add("active-exit");

                // Change page AFTER animation completes
                setTimeout(() => {
                    window.location = href;
                }, 900);
            });
        }
    });
    // ================================
// EVENTS PAGE AUTO-SCROLL TO ITEM
// ================================
if (window.location.pathname.includes("events.php")) {

    const targetId = sessionStorage.getItem("jumpToEvent");
    if (targetId) {
        sessionStorage.removeItem("jumpToEvent");

        const targetItem = document.querySelector(`[data-event-id="${targetId}"]`);
        if (targetItem) {

            // Scroll to item
            setTimeout(() => {
                targetItem.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }, 300);

            // Temporary highlight
            targetItem.classList.add("event-highlight");

            setTimeout(() => {
                targetItem.classList.remove("event-highlight");
            }, 5000);

          
            }
              
    }
}

});
// ================================
// HOMEPAGE → EVENTS PAGE SCROLL LINK
// ================================
document.querySelectorAll('.event-home').forEach(item => {

    item.addEventListener('click', () => {
        const id = item.dataset.eventId;
        if (!id) return;

        // store target event id
        sessionStorage.setItem("jumpToEvent", id);

        // run transition animation
        sessionStorage.setItem("liella-transition", "1");

        const frame = document.getElementById("transition-frame");
        frame.classList.add("active-exit");

        setTimeout(() => {
            window.location.href = "events.php?event=" + id;
        }, 900);
    });
});
document.querySelectorAll(".password-toggle").forEach(icon => {
    icon.addEventListener("click", () => {
        const target = document.getElementById(icon.dataset.target);
        const isPassword = target.type === "password";

        target.type = isPassword ? "text" : "password";
        icon.classList.toggle("ri-eye-line");
        icon.classList.toggle("ri-eye-off-line");
    });
});
// FORCE SUCCESS "GO HOME" BUTTON TO ALWAYS WORK
document.addEventListener("click", (e) => {
    if (e.target.closest("#goHomeBtn")) {

        // Use transition
        const frame = document.getElementById("transition-frame");
        sessionStorage.setItem("liella-transition", "1");

        frame.classList.add("active-exit");

        setTimeout(() => {
            window.location = "index.php";
        }, 900);
    }
});
