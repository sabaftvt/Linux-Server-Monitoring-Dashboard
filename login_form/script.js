document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
    errorMessage.style.display = 'none';
    
    try {
        const response = await fetch('http://localhost/system_monitoring/login_form/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        });
        
        if (!response.ok) throw new Error('Server response is not valid');
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = '../main_form/index.html';
        } else {
            throw new Error(data.message || 'Login error');
        }
    } catch(error) {
        errorMessage.textContent = error.message;
        errorMessage.style.display = 'block';
        console.error('Error:', error);
    }
});