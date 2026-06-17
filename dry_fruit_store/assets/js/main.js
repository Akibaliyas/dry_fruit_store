// Add to cart animation
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 200);
        
        // You can add AJAX cart update here
    });
});

// Form validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        let required = this.querySelectorAll('[required]');
        let valid = true;
        
        required.forEach(field => {
            if(!field.value.trim()) {
                field.style.borderColor = 'red';
                valid = false;
            } else {
                field.style.borderColor = '#ddd';
            }
        });
        
        if(!valid) {
            e.preventDefault();
            alert('Please fill all required fields');
        }
    });
});

// Product image preview
function previewImage(input) {
    if(input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('#image-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}