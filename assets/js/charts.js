/**
 * NEXORA CONSULTING GROUP
 * Gráficos Canvas API Nativo
 * Sin librerías externas
 */

function drawBarChart(canvasId, data, labels) {
    const canvas = document.getElementById(canvasId);
    if(!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const padding = 50;
    const barWidth = (width - padding*2) / data.length - 30;
    const maxValue = Math.max(...data) * 1.15;
    const borderRadius = 6;
    
    // Limpiar canvas
    ctx.clearRect(0, 0, width, height);
    
    // Fondo
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    
    // Líneas de grid con opacidad
    ctx.strokeStyle = 'rgba(229, 231, 235, 0.6)';
    ctx.lineWidth = 1;
    ctx.setLineDash([4, 4]);
    for(let i = 0; i <= 5; i++) {
        const y = padding + (height - padding*2) * i / 5;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(width - padding, y);
        ctx.stroke();
    }
    ctx.setLineDash([]);
    
    // Animación dibujar barras
    let animationProgress = 0;
    const animationDuration = 600;
    const startTime = performance.now();
    
    function animate() {
        animationProgress = Math.min(1, (performance.now() - startTime) / animationDuration);
        const easeProgress = 1 - Math.pow(1 - animationProgress, 3);
        
        ctx.clearRect(padding, padding, width - padding*2, height - padding*2);
        
        // Dibujar barras
        data.forEach((value, index) => {
            const barHeight = (value / maxValue) * (height - padding*2) * easeProgress;
            const x = padding + index * ((width - padding*2) / data.length) + 15;
            const y = height - padding - barHeight;
            
            // Sombra barra
            ctx.shadowColor = 'rgba(26, 39, 68, 0.15)';
            ctx.shadowBlur = 8;
            ctx.shadowOffsetY = 4;
            
            // Gradiente premium
            const gradient = ctx.createLinearGradient(x, y, x, height - padding);
            gradient.addColorStop(0, '#d4b85c');
            gradient.addColorStop(0.5, '#c9a84c');
            gradient.addColorStop(1, '#1a2744');
            
            ctx.fillStyle = gradient;
            
            // Barra con bordes redondeados
            ctx.beginPath();
            ctx.moveTo(x + borderRadius, y);
            ctx.lineTo(x + barWidth - borderRadius, y);
            ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + borderRadius);
            ctx.lineTo(x + barWidth, height - padding);
            ctx.lineTo(x, height - padding);
            ctx.lineTo(x, y + borderRadius);
            ctx.quadraticCurveTo(x, y, x + borderRadius, y);
            ctx.closePath();
            ctx.fill();
            
            ctx.shadowColor = 'transparent';
            
            // Etiqueta valor
            if(animationProgress > 0.7) {
                ctx.fillStyle = '#1a2744';
                ctx.font = 'bold 13px Segoe UI';
                ctx.textAlign = 'center';
                ctx.globalAlpha = (animationProgress - 0.7) / 0.3;
                ctx.fillText(value + 'h', x + barWidth/2, y - 10);
                ctx.globalAlpha = 1;
            }
        });
        
        if(animationProgress < 1) {
            requestAnimationFrame(animate);
        }
    }
    
    animate();
    
    // Etiquetas eje X
    data.forEach((value, index) => {
        const x = padding + index * ((width - padding*2) / data.length) + 15 + barWidth/2;
        ctx.fillStyle = '#6b7280';
        ctx.font = '13px Segoe UI';
        ctx.textAlign = 'center';
        ctx.fillText(labels[index], x, height - 20);
    });
}

function drawDonutChart(canvasId, data, colors) {
    const canvas = document.getElementById(canvasId);
    if(!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(centerX, centerY) - 40;
    const innerRadius = radius * 0.6;
    
    const total = data.reduce((a, b) => a + b, 0);
    let currentAngle = -Math.PI / 2;
    
    data.forEach((value, index) => {
        const sliceAngle = (value / total) * 2 * Math.PI;
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
        ctx.arc(centerX, centerY, innerRadius, currentAngle + sliceAngle, currentAngle, true);
        ctx.closePath();
        ctx.fillStyle = colors[index];
        ctx.fill();
        
        currentAngle += sliceAngle;
    });
    
    // Centro
    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(centerX, centerY, innerRadius, 0, 2 * Math.PI);
    ctx.fill();
}