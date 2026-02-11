<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Feliz Cumpleaños Pulga Aventurera!</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Fredoka+One&family=Pacifico&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            font-family: 'Fredoka One', cursive;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        /* Elementos de fondo flotantes */
        .background-element {
            position: absolute;
            opacity: 0.2;
            color: #fff;
            z-index: 0;
            pointer-events: none; /* Para que no interfieran con el click */
        }

        .container {
            text-align: center;
            z-index: 10;
            position: relative;
            perspective: 1000px;
            width: 90%;
            max-width: 800px;
        }

        h1 {
            font-family: 'Bangers', cursive;
            font-size: clamp(3rem, 10vw, 7rem); /* Tamaño responsivo */
            color: #ff6b6b;
            text-shadow: 4px 4px 0px #2f3542;
            margin: 0;
            line-height: 1.1;
        }

        /* Clase específica para animar las partes del título */
        .title-part {
            display: block; /* Fuerza que cada palabra esté en su línea */
            opacity: 0; /* Empieza invisible */
            transform: translateY(-100px) rotate(-10deg); /* Posición inicial para la animación */
        }

        h2 {
            font-family: 'Pacifico', cursive;
            font-size: clamp(2rem, 5vw, 4rem);
            color: #5f27cd;
            text-shadow: 2px 2px 0px #fff;
            margin-top: 20px;
            opacity: 0;
        }

        .cta-btn {
            margin-top: 40px;
            padding: 15px 40px;
            font-size: 1.5rem;
            border: none;
            border-radius: 50px;
            background: #ff9f43;
            color: white;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            font-family: 'Fredoka One', cursive;
            transition: transform 0.1s;
            opacity: 0;
        }

        .cta-btn:active {
            transform: scale(0.95);
        }

        .icon-adventure {
            font-size: clamp(2rem, 5vw, 3rem);
            color: #ff9f43;
            margin: 0 15px;
            display: inline-block;
            vertical-align: middle;
        }

    </style>
</head>
<body>

    <i class="fa-solid fa-cloud background-element" style="top: 10%; left: 10%; font-size: 8rem;"></i>
    <i class="fa-solid fa-cloud background-element" style="top: 20%; right: 15%; font-size: 6rem;"></i>
    <i class="fa-solid fa-map-location-dot background-element" style="bottom: 10%; left: 5%; font-size: 5rem;"></i>
    <i class="fa-solid fa-compass background-element" style="bottom: 15%; right: 10%; font-size: 5rem;"></i>
    <i class="fa-solid fa-mountain-sun background-element" style="top: 50%; left: 80%; font-size: 4rem;"></i>
    <i class="fa-solid fa-paper-plane background-element" style="top: 15%; left: 50%; font-size: 3rem;"></i>

    <div class="container">
        <h1 id="title">
            <span class="title-part">¡FELIZ</span>
            <span class="title-part">CUMPLEAÑOS!</span>
        </h1>

        <h2 id="subtitle">
            <i class="fa-solid fa-rocket icon-adventure"></i>
            Pulga Aventurera
            <i class="fa-solid fa-binoculars icon-adventure"></i>
        </h2>

        <button class="cta-btn" id="partyBtn">¡A CELEBRAR!</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        // --- 1. ANIMACIÓN DE ENTRADA (GSAP) ---
        const tl = gsap.timeline();

        // Animar fondo (nubes y elementos) flotando
        gsap.to(".background-element", {
            y: "random(-20, 20)",
            x: "random(-10, 10)",
            rotation: "random(-5, 5)",
            duration: 4,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut",
            stagger: 0.5
        });

        // Secuencia de entrada del texto CORREGIDA
        tl.to(".title-part", {
            duration: 1.2,
            y: 0,           // Vuelve a su posición original
            opacity: 1,     // Se hace visible
            rotate: 0,      // Se endereza
            stagger: 0.3,   // Tiempo entre la primera y segunda palabra
            ease: "elastic.out(1, 0.5)" // Efecto rebote
        })
        .to("#subtitle", {
            duration: 0.8,
            opacity: 1,
            y: 0,
            scale: 1.1,
            ease: "back.out(1.7)"
        }, "-=0.5")
        .to(".icon-adventure", {
            rotation: 360,
            duration: 1,
            ease: "back.out(1.7)"
        }, "-=0.8")
        .to(".cta-btn", {
            duration: 0.5,
            opacity: 1,
            y: 0,
            ease: "power2.out",
            onComplete: launchInitialConfetti
        });

        // --- 2. LÓGICA DE CONFETI ---

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        function launchInitialConfetti() {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }

        // --- 3. INTERACTIVIDAD ---

        // Botón principal
        document.getElementById('partyBtn').addEventListener('click', (e) => {
            // Explosión central grande
            confetti({
                particleCount: 150,
                spread: 100,
                origin: { y: 0.6 },
                colors: ['#ff6b6b', '#feca57', '#48dbfb', '#ff9f43'],
                scalar: 1.2
            });

            // Animación de rebote en el botón
            gsap.from(".cta-btn", {
                scale: 0.9,
                duration: 0.1,
                yoyo: true,
                repeat: 1
            });

            // Animación extra en los iconos al celebrar
            gsap.to(".icon-adventure", {
                rotation: "+=360",
                duration: 0.5,
                ease: "back.out(1.7)"
            });
        });

        // Click en cualquier parte
        document.addEventListener('click', (e) => {
            if(e.target.id === 'partyBtn') return;

            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;

            confetti({
                particleCount: 30,
                spread: 50,
                origin: { x: x, y: y },
                scalar: 0.8,
                shapes: ['circle', 'square'] // Formas variadas
            });
        });

        // Animación continua suave para el nombre
        gsap.to("#subtitle", {
            y: 5,
            duration: 2,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });

    </script>
</body>
</html>
