<footer class="app-footer">
    <div class="footer-container">
        
        <!-- Col 1: Marca -->
        <div class="footer-brand">
            <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="SENA" />
            <div>
                <strong>SENA Regional Guaviare</strong>
                <span>Industria y Tecnología</span>
            </div>
        </div>

        <!-- Col 2: Social -->
        <div class="footer-social">
            <span>Conéctate con nosotros</span>
            <div class="social-links">
                <a href="https://www.facebook.com/SENA" target="_blank" rel="noopener">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/SENAComunica" target="_blank" rel="noopener">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/school/senaoficial" target="_blank" rel="noopener">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <!-- Col 3: Meta -->
        <div class="footer-meta">
            <span>Versión 3.2.0</span>
            <span>info@dataguaviare.com.co</span>
        </div>
        
    </div>

    <div class="footer-bottom">
        © {{ now()->format('Y') }} SENA · Plataforma Académica
    </div>
</footer>
