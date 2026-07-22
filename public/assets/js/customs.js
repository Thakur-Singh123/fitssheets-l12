document.querySelectorAll('.toggle-pass').forEach(function(icon) {
    icon.addEventListener('click', function() {
        let input = this.previousElementSibling;
        if(input.type === 'password'){
            input.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        }else{
            input.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.pagination a').forEach(function(link){

        let url = new URL(link.href);

        if(url.searchParams.get('page') == 1){
            url.searchParams.delete('page');
            link.href = url.pathname + (url.search ? url.search : '');
        }

    });

});
document.getElementById('avtar').onchange = function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('.profile-preview').src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
};
document.getElementById('phone_no').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
});
$(document).ready(function () {
    setTimeout(function () {
        $('.error-alert').fadeOut(500);
    }, 5000);
});

