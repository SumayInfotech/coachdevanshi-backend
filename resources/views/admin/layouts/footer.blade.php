
<footer class="footer">
    <div class="container">
        <div class="row align-items-center flex-row-reverse">
            <div class="col-md-12 col-sm-12 text-center">
                 {{ setting('footer_text') ? setting('footer_text') : '© ' . date('Y') . ' Admin Panel. All rights reserved.'}} 
            </div>
        </div>
    </div>
</footer>
