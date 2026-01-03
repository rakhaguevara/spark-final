    </div> <!-- .admin-layout -->
    
    <script>
        // Flash message auto hide
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('.admin-flash-message');
            flashMessages.forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>

