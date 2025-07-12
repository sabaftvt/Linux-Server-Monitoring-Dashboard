document.addEventListener('DOMContentLoaded', function() {
    // مقداردهی اولیه نوارهای پیشرفت
    initProgressBars();
    
    // ایجاد نمودارها
    createCharts();
});

function initProgressBars() {
    document.querySelectorAll('.progress-fill').forEach(bar => {
        const value = parseFloat(bar.dataset.value) || 0;
        const percentage = Math.min(100, Math.max(0, value));
        
        // تنظیم عرض و رنگ بر اساس مقدار
        bar.style.width = `${percentage}%`;
        bar.parentElement.nextElementSibling.textContent = `${percentage}%`;
        
        // تغییر رنگ بر اساس مقدار
        if (percentage > 60) {
            bar.style.backgroundColor = '#e74c3c'; // قرمز
        } else if (percentage > 30) {
            bar.style.backgroundColor = '#f3b312'; // نارنجی
        } else {
            bar.style.backgroundColor = '#2ecc71'; // سبز
        }
    });
}

function createCharts() {
    const servers = window.serversData;
    
    // داده‌های برای نمودارها
    const labels = servers.map(server => server.hostname);
    const cpuData = servers.map(server => parseFloat(server.cpu_usage) || 0);
    const memoryData = servers.map(server => parseFloat(server.memory_usage) || 0);
    const diskData = servers.map(server => parseFloat(server.disk_usage) || 0);
    
    // رنگ‌ها بر اساس مقدار
    const cpuColors = cpuData.map(value => getColorForValue(value));
    const memoryColors = memoryData.map(value => getColorForValue(value));
    const diskColors = diskData.map(value => getColorForValue(value));
    
    // نمودار CPU
    const cpuCtx = document.getElementById('cpuChart').getContext('2d');
    new Chart(cpuCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'مصرف CPU (%)',
                data: cpuData,
                backgroundColor: cpuColors,
                borderColor: cpuColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1
            }]
        },
        options: getChartOptions('مقایسه مصرف CPU سرورها')
    });
    
    // نمودار Memory
    const memoryCtx = document.getElementById('memoryChart').getContext('2d');
    new Chart(memoryCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'مصرف RAM (%)',
                data: memoryData,
                backgroundColor: memoryColors,
                borderColor: memoryColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1
            }]
        },
        options: getChartOptions('مقایسه مصرف RAM سرورها')
    });
    
    // نمودار Disk
    const diskCtx = document.getElementById('diskChart').getContext('2d');
    new Chart(diskCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'مصرف دیسک (%)',
                data: diskData,
                backgroundColor: diskColors,
                borderColor: diskColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1
            }]
        },
        options: getChartOptions('مقایسه مصرف دیسک سرورها')
    });
}

function getColorForValue(value) {
    if (value > 60) return 'rgba(231, 76, 60, 0.7)'; // قرمز
    if (value > 30) return 'rgba(243, 156, 18, 0.7)'; // نارنجی
    return 'rgba(46, 204, 113, 0.7)'; // سبز
}

function getChartOptions(title) {
    return {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                rtl: true
            },
            title: {
                display: true,
                text: title,
                font: {
                    size: 16
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    };
}