        </div>
    </main>
    <script>
        document.querySelectorAll('.sidebar-menu a').forEach(function(link) {
            link.addEventListener('click', function() {
                document.getElementById('sidebar').classList.remove('mobile-open');
            });
        });
    </script>
</body>
</html>
