function previewSelectedImage(input) {
    const preview = document.getElementById('imagePreview');
    if (!preview || !input || !input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];
    if (!file.type.startsWith('image/')) {
        preview.innerHTML = '<span>Choose a valid image file</span>';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (event) {
        preview.innerHTML = `<img src="${event.target.result}" alt="Selected project preview">`;
    };
    reader.readAsDataURL(file);
}

function exploreProjects(button) {
    const projectsSection = document.getElementById('projects');
    if (projectsSection) {
        projectsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (button) {
        const originalText = button.textContent;
        button.textContent = 'Opening Projects...';
        setTimeout(() => {
            button.textContent = originalText;
        }, 800);
    }
}

/* =============================================
   MOBILE HAMBURGER MENU
   ============================================= */
function toggleMobileMenu() {
    const btn   = document.getElementById('hamburgerBtn');
    const links = document.getElementById('navLinks');
    if (!btn || !links) return;
    btn.classList.toggle('open');
    links.classList.toggle('open');
}

function closeMobileMenu() {
    const btn   = document.getElementById('hamburgerBtn');
    const links = document.getElementById('navLinks');
    if (!btn || !links) return;
    btn.classList.remove('open');
    links.classList.remove('open');
}

// Close menu when clicking outside the nav
document.addEventListener('click', (e) => {
    const nav = document.getElementById('mainNav');
    if (nav && !nav.contains(e.target)) {
        closeMobileMenu();
    }
});


function showLogin(){
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    if (!loginForm || !signupForm) return;
    loginForm.classList.add("active");
    signupForm.classList.remove("active");
}

function showSignup(){
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    if (!loginForm || !signupForm) return;
    signupForm.classList.add("active");
    loginForm.classList.remove("active");
}

/* AI Hub Tab Switching */
function switchAITab(tabId) {
    const contents = document.querySelectorAll('.ai-tab-content');
    contents.forEach(content => content.classList.remove('active'));

    const buttons = document.querySelectorAll('.ai-tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    const activeTab = document.getElementById(tabId);
    if (!activeTab) return;
    activeTab.classList.add('active');

    const currentBtn = Array.from(buttons).find(btn => (btn.getAttribute('onclick') || '').includes(tabId));
    if (currentBtn) currentBtn.classList.add('active');
}

/* Tool 1: AI Code Companion (Typewriter Simulation) */
let isTyping = false;
function generateAICode() {
    if (isTyping) return;
    const promptInput = document.getElementById('codePrompt');
    const terminal = document.getElementById('terminalOutput');
    if (!promptInput || !terminal) return;
    const prompt = promptInput.value.toLowerCase().trim();
    terminal.innerHTML = '';
    isTyping = true;

    let codeSnippet = '';
    if (prompt.includes('button')) {
        codeSnippet = `/* Glassmorphic Button */\n.btn-glass {\n    background: rgba(255, 255, 255, 0.05);\n    backdrop-filter: blur(10px);\n    -webkit-backdrop-filter: blur(10px);\n    border: 1px solid rgba(255, 255, 255, 0.1);\n    border-radius: 12px;\n    color: #ffffff;\n    padding: 12px 24px;\n    font-weight: 600;\n    cursor: pointer;\n    transition: all 0.3s ease;\n}\n.btn-glass:hover {\n    background: rgba(255, 255, 255, 0.1);\n    box-shadow: 0 0 15px rgba(123, 44, 255, 0.4);\n    transform: translateY(-2px);\n}`;
    } else if (prompt.includes('card')) {
        codeSnippet = `/* Neon Glowing Card */\n.card-neon {\n    background: #09091A;\n    border: 1px solid #7B2CFF;\n    box-shadow: 0 0 20px rgba(123, 44, 255, 0.2);\n    border-radius: 20px;\n    padding: 24px;\n    transition: transform 0.3s, border-color 0.3s;\n}\n.card-neon:hover {\n    transform: translateY(-5px);\n    border-color: #FF00AA;\n    box-shadow: 0 0 30px rgba(255, 0, 170, 0.4);\n}`;
    } else {
        codeSnippet = `/* Custom Styled Component */\n.custom-component {\n    display: flex;\n    align-items: center;\n    background: linear-gradient(135deg, #7B2CFF 0%, #FF00AA 100%);\n    border-radius: 16px;\n    padding: 20px;\n    color: #ffffff;\n    box-shadow: 0 10px 30px rgba(123, 44, 255, 0.2);\n    transition: all 0.3s ease;\n}\n.custom-component:hover {\n    filter: brightness(1.1);\n    transform: scale(1.02);\n}`;
    }

    let i = 0;
    terminal.innerHTML = '> Accessing Mi-AI Studio...\n> Drafting code snippet...\n\n';

    function type() {
        if (i < codeSnippet.length) {
            terminal.innerHTML += codeSnippet.charAt(i);
            i++;
            setTimeout(type, 15);
        } else {
            isTyping = false;
        }
    }
    setTimeout(type, 800);
}

/* Tool 2: Theme Palette Generator */
function generateAIPalette() {
    const promptInput = document.getElementById('palettePrompt');
    const output = document.getElementById('paletteOutput');
    if (!promptInput || !output) return;
    const prompt = promptInput.value.toLowerCase().trim();
    output.innerHTML = '<p class="placeholder-text">Analyzing aesthetics...</p>';

    let colors = [];
    if (prompt.includes('cyberpunk') || prompt.includes('neon')) {
        colors = ['#09091A', '#7B2CFF', '#FF00AA', '#00FF66'];
    } else if (prompt.includes('ocean') || prompt.includes('blue') || prompt.includes('water')) {
        colors = ['#0B132B', '#1C2541', '#3A506B', '#5BC0BE'];
    } else if (prompt.includes('forest') || prompt.includes('mist') || prompt.includes('nature') || prompt.includes('green')) {
        colors = ['#1A3A2B', '#2C5E43', '#4E9F70', '#8ECA99'];
    } else if (prompt.includes('retro') || prompt.includes('sunset') || prompt.includes('wave')) {
        colors = ['#2E112D', '#540032', '#82003D', '#F8A83D'];
    } else {
        colors = ['#12082d', '#7B2CFF', '#FF00AA', '#cfcfff'];
    }

    setTimeout(() => {
        let htmlContent = '<div class="palette-grid">';
        colors.forEach(color => {
            htmlContent += `
                <div class="palette-swatch">
                    <div class="swatch-color" style="background-color: ${color};"></div>
                    <span class="swatch-hex">${color}</span>
                </div>
            `;
        });
        htmlContent += '</div>';
        output.innerHTML = htmlContent;
    }, 600);
}

/* Tool 3: Project Idea Generator */
function generateAIIdeas() {
    const stackInput = document.getElementById('ideaTechStack');
    const output = document.getElementById('ideasOutput');
    if (!stackInput || !output) return;
    const stack = stackInput.value;
    output.innerHTML = '<p class="placeholder-text">Brainstorming options...</p>';

    let ideas = [];
    if (stack.includes('React & Node.js')) {
        ideas = [
            { title: '🔄 Collaborative Taskboard', desc: 'Real-time project boards featuring drag-and-drop workspace columns using Socket.io and Express.' },
            { title: '🎙️ AI Mock-Interview Sandbox', desc: 'A frontend client that connects speech synthesis API to GPT prompts for developers preparing for tech rounds.' },
            { title: '💻 Devs-Connect Portal', desc: 'A matching hub where backend engineers find frontend developers to team up for hackathons.' }
        ];
    } else if (stack.includes('Next.js & Supabase')) {
        ideas = [
            { title: '📈 SaaS Billing & Analytics Hub', desc: 'An dashboard client with charts showing usage stats, featuring user auth and Stripe subscription billing.' },
            { title: '🛒 Digital Marketplace assets', desc: 'A storefront dedicated to UI asset packs, design templates, and download security with Supabase buckets.' },
            { title: '💬 Real-time Space Board', desc: 'A thread board with markdown post support, nested comment threads, upvotes, and real-time live sync.' }
        ];
    } else {
        ideas = [
            { title: '🎛️ Glassmorphic Code Editor', desc: 'A frontend sandbox that compiles HTML/CSS snippets inside the browser using local storage.' },
            { title: '🎵 Web Audio Visualizer', desc: 'A sleek custom player using standard Web Audio API nodes to render floating light waveforms.' },
            { title: '⏱️ Pomodoro Tech Flow', desc: 'A dark mode focusing timer with customizable interval options, sound profiles, and work statistics.' }
        ];
    }

    setTimeout(() => {
        let htmlContent = '';
        ideas.forEach(idea => {
            htmlContent += `
                <div class="idea-item">
                    <h4>${idea.title}</h4>
                    <p>${idea.desc}</p>
                </div>
            `;
        });
        output.innerHTML = htmlContent;
    }, 600);
}

/* =============================================
   TOOL 4: CSS ANIMATOR
   ============================================= */
function generateCSSAnimation() {
    const type = document.getElementById('animationType')?.value || 'bounce';
    const output = document.getElementById('cssAnimatorOutput');
    if (!output) return;

    const animations = {
        bounce: `@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-18px); }
}

.bounce-element {
    animation: bounce 1.8s infinite ease-in-out;
}`,
        fade: `@keyframes fadeInOut {
    0% { opacity: 0; }
    25% { opacity: 1; }
    75% { opacity: 1; }
    100% { opacity: 0; }
}

.fade-element {
    animation: fadeInOut 3s ease-in-out infinite;
}`,
        slide: `@keyframes slideIn {
    0% { opacity: 0; transform: translateX(-40px); }
    100% { opacity: 1; transform: translateX(0); }
}

.slide-element {
    animation: slideIn 0.7s ease forwards;
}`,
        pulse: `@keyframes pulseGlow {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 rgba(255,255,255,0); }
    50% { transform: scale(1.03); box-shadow: 0 0 22px rgba(123,44,255,0.25); }
}

.pulse-element {
    animation: pulseGlow 1.8s infinite ease-in-out;
}`,
        spin: `@keyframes spinRound {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spin-element {
    animation: spinRound 1.5s linear infinite;
}`,
        shake: `@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-8px); }
    40%, 80% { transform: translateX(8px); }
}

.shake-element {
    animation: shake 0.8s ease-in-out infinite;
}`,
        typewriter: `@keyframes typewrite {
    from { width: 0; }
    to { width: 100%; }
}

.typewriter {
    overflow: hidden;
    white-space: nowrap;
    border-right: 2px solid rgba(255,255,255,0.8);
    animation: typewrite 3.5s steps(30) infinite;
}`,
        float: `@keyframes floatEase {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

.float-element {
    animation: floatEase 4s ease-in-out infinite;
}`
    };

    output.textContent = animations[type] || animations.bounce;
}

function scrollToAIHub(tabId) {
    const section = document.getElementById('ai-hub');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(() => {
        if (tabId) {
            switchAITab(tabId);
        }
    }, 350);
}

function showToast(message, status = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${status}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-hide');
    }, 3000);
    setTimeout(() => {
        toast.remove();
    }, 3800);
}

function handleContactSubmit(event) {
    const name = document.getElementById('contactName')?.value.trim();
    const email = document.getElementById('contactEmail')?.value.trim();
    const message = document.getElementById('contactMessage')?.value.trim();

    if (!name || !email || !message) {
        event.preventDefault();
        showToast('Please fill in all required fields.', 'error');
        return false;
    }

    return true;
}

window.addEventListener('load', () => {
    const params = new URLSearchParams(window.location.search);
    const message = params.get('message');
    const saved = params.get('saved');
    const error = params.get('error');

    if (message && saved === '1') {
        showToast(message, 'success');
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (message && error) {
        showToast(message, 'error');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

const testimonialSlides = document.querySelectorAll('.testimonial-slide');
const carouselDots = document.querySelectorAll('.carousel-dot');
let currentTestimonialIndex = 0;

function updateTestimonialCarousel(index) {
    if (!testimonialSlides.length) return;
    currentTestimonialIndex = (index + testimonialSlides.length) % testimonialSlides.length;

    testimonialSlides.forEach((slide, idx) => {
        slide.classList.toggle('active', idx === currentTestimonialIndex);
    });

    carouselDots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === currentTestimonialIndex);
    });
}

function prevTestimonial() {
    updateTestimonialCarousel(currentTestimonialIndex - 1);
}

function nextTestimonial() {
    updateTestimonialCarousel(currentTestimonialIndex + 1);
}

function goToTestimonial(index) {
    updateTestimonialCarousel(index);
}

const typewriterWords = ['web experiences.', 'digital products.', 'interactive interfaces.', 'beautiful apps.'];
let typewriterWordIndex = 0;
let typewriterCharIndex = 0;
let isDeleting = false;

function updateTypewriter() {
    const element = document.getElementById('typewriter');
    if (!element) return;

    const currentWord = typewriterWords[typewriterWordIndex];
    if (isDeleting) {
        typewriterCharIndex -= 1;
        element.textContent = currentWord.slice(0, typewriterCharIndex);
        if (typewriterCharIndex <= 0) {
            isDeleting = false;
            typewriterWordIndex = (typewriterWordIndex + 1) % typewriterWords.length;
            setTimeout(updateTypewriter, 300);
            return;
        }
    } else {
        typewriterCharIndex += 1;
        element.textContent = currentWord.slice(0, typewriterCharIndex);
        if (typewriterCharIndex === currentWord.length) {
            isDeleting = true;
            setTimeout(updateTypewriter, 1500);
            return;
        }
    }

    const delay = isDeleting ? 60 : 100;
    setTimeout(updateTypewriter, delay);
}

function updateCharCounter() {
    const textarea = document.getElementById('contactMessage');
    const counter = document.getElementById('charCounter');
    if (!textarea || !counter) return;
    counter.textContent = `${textarea.value.length} / 500`;
}

function animateStatsCounter(targetEl) {
    const targetValue = parseInt(targetEl.dataset.target, 10) || 0;
    const duration = 1500;
    const startTime = performance.now();
    const initialValue = 0;

    function tick(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        const currentValue = Math.floor(progress * (targetValue - initialValue) + initialValue);
        targetEl.textContent = currentValue;
        if (progress < 1) {
            requestAnimationFrame(tick);
        } else {
            targetEl.textContent = targetValue;
        }
    }

    requestAnimationFrame(tick);
}

function initStatsCounters() {
    const statElements = document.querySelectorAll('.stat-number');
    if (!statElements.length) return;

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const targetEl = entry.target;
                animateStatsCounter(targetEl);
                obs.unobserve(targetEl);
            }
        });
    }, {
        threshold: 0.4
    });

    statElements.forEach(el => {
        el.textContent = '0';
        observer.observe(el);
    });
}

const contactMessageField = document.getElementById('contactMessage');
if (contactMessageField) {
    contactMessageField.addEventListener('input', updateCharCounter);
    updateCharCounter();
}

updateTypewriter();
initStatsCounters();

/* =============================================
   SCROLL REVEAL — IntersectionObserver
   ============================================= */
(function initScrollReveal() {
    const revealSelectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale';
    const allReveal = document.querySelectorAll(revealSelectors);

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -50px 0px'
    });

    allReveal.forEach(el => observer.observe(el));
})();

/* =============================================
   SCROLL PROGRESS BAR
   ============================================= */
const progressBar = document.getElementById('scroll-progress');

function updateScrollProgress() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    if (progressBar) progressBar.style.width = pct + '%';
}

window.addEventListener('scroll', updateScrollProgress, { passive: true });

/* =============================================
   NAV SHRINK + ACTIVE LINK + BACK TO TOP
   ============================================= */
const navEl       = document.querySelector('nav');
const sections    = document.querySelectorAll('section[id]');
const navLinks    = document.querySelectorAll('nav ul li a:not(.nav-login-btn)');
const backToTopBtn = document.getElementById('backToTop');

function onScroll() {
    const scrollY = window.scrollY;

    /* Nav shrink */
    navEl && (scrollY > 60
        ? navEl.classList.add('scrolled')
        : navEl.classList.remove('scrolled'));

    /* Back-to-top button */
    backToTopBtn && (scrollY > 400
        ? backToTopBtn.classList.add('visible')
        : backToTopBtn.classList.remove('visible'));

    /* Active nav link highlighting */
    let current = '';
    sections.forEach(section => {
        if (scrollY >= section.offsetTop - 130) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('nav-active');
        const href = link.getAttribute('href');
        if (href === '#' + current || (current === '' && href === '#')) {
            link.classList.add('nav-active');
        }
    });
}

window.addEventListener('scroll', onScroll, { passive: true });

/* =============================================
   BACK TO TOP
   ============================================= */
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* =============================================
   PARALLAX ORBS — subtle mouse parallax
   ============================================= */
const orbs = document.querySelectorAll('.parallax-orb');
document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth  - 0.5) * 30;
    const y = (e.clientY / window.innerHeight - 0.5) * 30;
    orbs.forEach((orb, i) => {
        const depth = (i + 1) * 0.4;
        orb.style.transform = `translate(${x * depth}px, ${y * depth}px)`;
    });
}, { passive: true });

/* =============================================
   DEVELOPER SOCIAL HOMEPAGE INTERACTIONS
   ============================================= */
function initDeveloperSocial() {
    const searchInput = document.getElementById('globalSearch');
    const feedPosts = Array.from(document.querySelectorAll('.feed-post'));
    const storyCards = Array.from(document.querySelectorAll('.story-card'));

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();

            feedPosts.forEach(post => {
                const content = `${post.dataset.searchable || ''} ${post.textContent}`.toLowerCase();
                post.classList.toggle('search-hidden', query !== '' && !content.includes(query));
            });

            storyCards.forEach(story => {
                const content = story.textContent.toLowerCase();
                story.classList.toggle('search-hidden', query !== '' && !content.includes(query));
            });
        });
    }

    storyCards.forEach(story => {
        const activateStory = () => {
            storyCards.forEach(card => card.classList.remove('active'));
            story.classList.add('active');
            showToast(`${story.dataset.story || story.textContent.trim()} feed opened`, 'info');
        };

        story.addEventListener('click', activateStory);
        story.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                activateStory();
            }
        });
    });

    const quickPrompts = {
        bug: {
            subject: 'Need help debugging',
            message: 'I am stuck on a bug in my project. Here is what I expected, what happened, and the code I already tried:'
        },
        snippet: {
            subject: 'Useful code snippet',
            message: 'Sharing a small snippet that helped me today. It solves:'
        },
        launch: {
            subject: 'Project launch',
            message: 'I just launched a new project. Stack, features, and feedback I am looking for:'
        },
        collab: {
            subject: 'Looking for collaborators',
            message: 'I am looking for developers to collaborate on this idea. Skills needed, timeline, and next step:'
        }
    };

    document.querySelectorAll('[data-fill-post]').forEach(button => {
        button.addEventListener('click', () => {
            const prompt = quickPrompts[button.dataset.fillPost];
            const subject = document.getElementById('contactSubject');
            const message = document.getElementById('contactMessage');
            if (!prompt || !subject || !message) return;

            subject.value = prompt.subject;
            message.value = prompt.message;
            updateCharCounter();
            message.focus();
        });
    });

    document.querySelectorAll('[data-social-action]').forEach(button => {
        button.addEventListener('click', async () => {
            const action = button.dataset.socialAction;

            if (action === 'like') {
                const count = button.querySelector('span');
                const isLiked = button.classList.toggle('is-liked');
                if (count) {
                    const current = parseInt(count.textContent, 10) || 0;
                    count.textContent = String(current + (isLiked ? 1 : -1));
                }
                if (button.firstChild) {
                    button.firstChild.nodeValue = isLiked ? '♥ ' : '♡ ';
                }
                return;
            }

            if (action === 'share') {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    showToast('Link copied to clipboard.', 'success');
                } catch (error) {
                    showToast('Share this page URL with your developer friends.', 'info');
                }
                return;
            }

            if (action === 'comment') {
                showToast('Comment threads are coming next.', 'info');
                return;
            }

            if (action === 'join') {
                button.textContent = 'Joined';
                button.classList.add('is-liked');
                showToast('You joined the developer thread.', 'success');
            }
        });
    });

    document.querySelectorAll('[data-follow]').forEach(button => {
        button.addEventListener('click', () => {
            const isFollowing = button.classList.toggle('is-following');
            button.textContent = isFollowing ? 'Following' : 'Follow';
            showToast(isFollowing ? 'Coder followed.' : 'Coder unfollowed.', isFollowing ? 'success' : 'info');
        });
    });

    document.querySelectorAll('.post-menu').forEach(button => {
        button.addEventListener('click', () => {
            showToast('Post saved to your dev board.', 'success');
        });
    });
}

initDeveloperSocial();
