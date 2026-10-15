@if(session()->has('confetti'))
    <canvas id="confetti-canvas" style="position: fixed; inset: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 9999;"></canvas>
    <script>
        (function () {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            var canvas = document.getElementById('confetti-canvas');
            if (!canvas) {
                return;
            }

            var ctx = canvas.getContext('2d');
            var DPR = window.devicePixelRatio || 1;
            var W = window.innerWidth;
            var H = window.innerHeight;

            function resize() {
                W = window.innerWidth;
                H = window.innerHeight;
                canvas.width = W * DPR;
                canvas.height = H * DPR;
                ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
            }

            resize();
            window.addEventListener('resize', resize);

            var colors = ['#4f46e5', '#7c3aed', '#f59e0b', '#22c55e', '#ef4444', '#0ea5e9', '#ff8a00'];
            var particles = [];

            function burst(x, y, count, power) {
                for (var i = 0; i < count; i++) {
                    var angle = Math.random() * Math.PI * 2;
                    var speed = Math.random() * power + power * 0.3;
                    particles.push({
                        x: x,
                        y: y,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed - power * 0.7,
                        size: 6 + Math.random() * 8,
                        color: colors[(Math.random() * colors.length) | 0],
                        rot: Math.random() * Math.PI,
                        vr: (Math.random() - 0.5) * 0.3,
                        life: 1
                    });
                }
            }

            burst(W * 0.2, H * 0.75, 70, 12);
            burst(W * 0.8, H * 0.75, 70, 12);
            setTimeout(function () { burst(W * 0.5, H * 0.45, 90, 15); }, 250);

            var gravity = 0.26;
            var drag = 0.99;
            var start = performance.now();

            function frame(now) {
                var t = now - start;
                ctx.clearRect(0, 0, W, H);

                for (var i = particles.length - 1; i >= 0; i--) {
                    var p = particles[i];
                    p.vx *= drag;
                    p.vy = p.vy * drag + gravity;
                    p.x += p.vx;
                    p.y += p.vy;
                    p.rot += p.vr;

                    if (t > 2500) {
                        p.life -= 0.02;
                    }

                    if (p.life <= 0 || p.y > H + 40) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = Math.max(0, Math.min(1, p.life));
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rot);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                    ctx.restore();
                }

                if (particles.length > 0 && t < 6000) {
                    requestAnimationFrame(frame);
                } else {
                    window.removeEventListener('resize', resize);
                    canvas.remove();
                }
            }

            requestAnimationFrame(frame);
        })();
    </script>
@endif
