document.getElementById('addForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const ip = document.getElementById('ip').value.trim();
    const username = document.getElementById('username').value.trim();
    const pass = document.getElementById('pass').value;
    const messageDiv = document.getElementById('message');
    
    // پاک کردن پیام‌های قبلی
    messageDiv.textContent = '';
    messageDiv.className = '';
    
    try {
        // اعتبارسنجی IP
        if (!/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip)) {
            throw new Error('The IP format is invalid (example: 192.168.1.1)');
        }
        
        // اعتبارسنجی نام کاربری
        if (username.length < 3 || username.length > 50) {
            throw new Error('Username must be between 3 and 50 characters');
        }
        
        // اعتبارسنجی رمز عبور
        if (pass.length < 1) {
            throw new Error('Password is required');
        }

        // ارسال درخواست
        const response = await fetch('http://localhost/system_monitoring/add_form/add_record.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ip: ip,
                username: username,
                pass: pass
            })
        });

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }
        
        // نمایش پیام موفقیت
        messageDiv.textContent = data.message;
        messageDiv.className = 'success';
        document.getElementById('addForm').reset();
        
    } catch (error) {
        console.error('Error:', error);
        messageDiv.textContent = error.message;
        messageDiv.className = 'error';
    }
});

// هندلر برای فرم آپلود فایل
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('txtFile');
    const messageDiv = document.getElementById('uploadMessage');
    messageDiv.textContent = '';
    messageDiv.className = '';
    
    if (!fileInput.files.length) {
        showUploadMessage('Please select a file', 'error');
        return;
    }

    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = async function(e) {
        try {
            const content = e.target.result;
            const lines = content.split('\n').filter(line => line.trim() !== '');
            
            if (lines.length === 0) {
                throw new Error('The selected file is empty');
            }

            let successCount = 0;
            let errorMessages = [];
            
            // پردازش هر خط
            for (const line of lines) {
                try {
                    const parts = line.split(',').map(item => item.trim());
                    if (parts.length !== 3) {
                        throw new Error('The format is invalid');
                    }
                    
                    const [ip, username, pass] = parts;
                    
                    // اعتبارسنجی داده‌های هر خط
                    if (!ip || !username || !pass) {
                        throw new Error('Empty values ​​are not allowed');
                    }
                    
                    // ارسال به سرور
                    const response = await fetch('http://localhost/system_monitoring/add_form/add_record.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            ip: ip,
                            username: username,
                            pass: pass
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        successCount++;
                    } else {
                        errorMessages.push(`Error in record ${ip}: ${data.message}`);
                    }
                } catch (lineError) {
                    errorMessages.push(`:Error processing line "${line}" - ${lineError.message}`);
                }
            }
            
            // نمایش نتایج
            let resultMessage = `:Number of records added ${successCount} از ${lines.length}`;
            if (errorMessages.length > 0) {
                resultMessage += '\n\n :Errors \n' + errorMessages.join('\n');
            }
            
            showUploadMessage(resultMessage, errorMessages.length > 0 ? 'error' : 'success');
            
        } catch (error) {
            showUploadMessage(':Error ' + error.message, 'error');
            console.error('Error:', error);
        }
    };

    reader.onerror = function() {
        showUploadMessage('Error reading file', 'error');
    };

    reader.readAsText(file);
});

function showUploadMessage(message, type) {
    const messageDiv = document.getElementById('uploadMessage');
    messageDiv.className = type;
    
    // اگر پیام طولانی است، از textarea استفاده کنید
    if (message.length > 100 || message.includes('\n')) {
        messageDiv.innerHTML = `<textarea readonly>${message}</textarea>`;
    } else {
        messageDiv.textContent = message;
    }
}