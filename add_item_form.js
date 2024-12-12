document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addItemForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // Limpiar los valores escaneados
        const partNumberInput = document.getElementById('part_number');
        const serialNumberInput = document.getElementById('serial_number');
        const quantityInput = document.getElementById('quantity');

        partNumberInput.value = partNumberInput.value.replace(/^P/, '').replace(/[\s-]/g, '');
        serialNumberInput.value = serialNumberInput.value.replace(/^1S/, '').replace(/[\s-]/g, '');
        quantityInput.value = quantityInput.value.replace(/^Q/, '').replace(/[\s-]/g, '');

        const formData = new FormData(this);

        fetch('add_item.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');

                    // Limpiar el formulario
                    this.reset();

                    // Establecer el foco en el primer campo de entrada
                    document.getElementById('part_number').focus();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar el item.', 'error');
            });
    });
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type} show`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.classList.remove('show');
        document.body.removeChild(notification);
    }, 5000);
}