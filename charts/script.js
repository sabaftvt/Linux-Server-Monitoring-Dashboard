document.addEventListener('DOMContentLoaded', function() {
    // ایجاد نمودارهای دایره‌ای برای هر سرور
    createCircleProgressBars();
});

function createCircleProgressBars() {
    document.querySelectorAll('.circle-progress').forEach(element => {
        const value = parseFloat(element.dataset.value) / 100;
        const color = element.dataset.color;
        
        const bar = new ProgressBar.Circle(element, {
            color: color,
            strokeWidth: 10,
            trailWidth: 8,
            trailColor: '#ecf0f1',
            easing: 'easeInOut',
            duration: 1400,
            text: {
                value: '0%',
                style: {
                    color: '#2c3e50',
                    position: 'absolute',
                    left: '50%',
                    top: '50%',
                    padding: 0,
                    margin: 0,
                    transform: {
                        prefix: true,
                        value: 'translate(-50%, -50%)'
                    },
                    fontSize: '1.5rem',
                    fontWeight: 'bold'
                }
            },
            step: (state, circle) => {
                circle.setText(Math.round(circle.value() * 100) + '%');
            }
        });
        
        bar.animate(value);
    });
}

// تابع برای به‌روزرسانی خودکار داده‌ها
function refreshData() {
    fetch('get_latest_data.php')
        .then(response => response.json())
        .then(data => {
            // به‌روزرسانی نمودارها با داده‌های جدید
            updateProgressBars(data);
        })
        .catch(error => console.error('Error:', error));
}

// تابع برای به‌روزرسانی نمودارها
function updateProgressBars(data) {
    data.forEach(server => {
        // به‌روزرسانی مصرف RAM
        const memoryElement = document.getElementById(`memory-${server.id}`);
        if (memoryElement) {
            const memoryValue = parseFloat(server.memory_usage) / 100;
            const memoryColor = getProgressColor(server.memory_usage);
            updateCircleProgress(memoryElement, memoryValue, memoryColor);
        }
        
        // به‌روزرسانی مصرف CPU
        const cpuElement = document.getElementById(`cpu-${server.id}`);
        if (cpuElement) {
            const cpuValue = parseFloat(server.cpu_usage) / 100;
            const cpuColor = getProgressColor(server.cpu_usage);
            updateCircleProgress(cpuElement, cpuValue, cpuColor);
        }
        
        // به‌روزرسانی مصرف دیسک
        const diskElement = document.getElementById(`disk-${server.id}`);
        if (diskElement) {
            const diskValue = parseFloat(server.disk_usage) / 100;
            const diskColor = getProgressColor(server.disk_usage);
            updateCircleProgress(diskElement, diskValue, diskColor);
        }
    });
}

// تابع برای به‌روزرسانی یک نمودار دایره‌ای
function updateCircleProgress(element, value, color) {
    const bar = new ProgressBar.Circle(element, {
        color: color,
        strokeWidth: 10,
        trailWidth: 8,
        trailColor: '#ecf0f1',
        easing: 'easeInOut',
        duration: 1000,
        text: {
            value: '0%',
            style: {
                color: '#2c3e50',
                position: 'absolute',
                left: '50%',
                top: '50%',
                padding: 0,
                margin: 0,
                transform: {
                    prefix: true,
                    value: 'translate(-50%, -50%)'
                },
                fontSize: '1.5rem',
                fontWeight: 'bold'
            }
        },
        step: (state, circle) => {
            circle.setText(Math.round(circle.value() * 100) + '%');
        }
    });
    
    bar.animate(value);
}

// تابع برای تعیین رنگ بر اساس مقدار
function getProgressColor(value) {
    value = parseFloat(value);
    if (value > 60) return '#e74c3c'; // قرمز
    if (value > 30) return '#f39c12'; // نارنجی
    return '#2ecc71'; // سبز
}

// به‌روزرسانی خودکار هر 30 ثانیه
setInterval(refreshData, 30000);