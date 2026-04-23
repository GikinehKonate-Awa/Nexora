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
    const padding = 40;
    const barWidth = (width - padding*2) / data.length - 20;
    const maxValue = Math.max(...data) * 1.1;
    
    // Limpiar canvas
    ctx.clearRect(0, 0, width, height);
    
    // Fondo
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    
    // Líneas de grid
    ctx.strokeStyle = '#e5e7eb';
    ctx.lineWidth = 1;
    for(let i = 0; i <= 5; i++) {
        const y = padding + (height - padding*2) * i / 5;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(width - padding, y);
        ctx.stroke();
    }
    
    // Dibujar barras
    data.forEach((value, index) => {
        const barHeight = (value / maxValue) * (height - padding*2);
        const x = padding + index * ((width - padding*2) / data.length) + 10;
        const y = height - padding - barHeight;
        
        // Gradiente barra
        const gradient = ctx.createLinearGradient(x, y, x, height - padding);
        gradient.addColorStop(0, '#c9a84c');
        gradient.addColorStop(1, '#1a2744');
        
        ctx.fillStyle = gradient;
        ctx.fillRect(x, y, barWidth, barHeight);
        
        // Etiqueta valor
        ctx.fillStyle = '#1a2744';
        ctx.font = 'bold 12px Segoe UI';
        ctx.textAlign = 'center';
        ctx.fillText(value + 'h', x + barWidth/2, y - 8);
        
        // Etiqueta eje X
        ctx.fillStyle = '#6b7280';
        ctx.font = '12px Segoe UI';
        ctx.fillText(labels[index], x + barWidth/2, height - 15);
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