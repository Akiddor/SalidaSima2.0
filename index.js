let selectedPallets = [];

document.addEventListener('DOMContentLoaded', function () {
    // Mostrar notificación si existe
    const notification = document.getElementById('notification');
    if (notification) {
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    }

    // Asociar eventos a los encabezados de acordeón
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            header.classList.toggle('active');
            const body = header.nextElementSibling;
            body.style.display = body.style.display === 'block' ? 'none' : 'block';
        });
    });

    // Asociar eventos a los botones de seleccionar pallet
    document.querySelectorAll('.btn-select-pallet').forEach(button => {
        button.addEventListener('click', function () {
            const palletId = this.getAttribute('data-pallet-id');

            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
                selectedPallets = selectedPallets.filter(id => id !== palletId);
            } else {
                this.classList.add('selected');
                selectedPallets.push(palletId);
            }

            const printContainer = document.getElementById('print-selected-container');
            printContainer.style.display = selectedPallets.length > 0 ? 'block' : 'none';
        });
    });

    // Asociar evento al botón de imprimir pallets seleccionados
    document.getElementById('print-selected-pallets').addEventListener('click', function () {
        const folio = prompt('Por favor ingrese el número de folio:');
        if (folio) {
            // Verificar si el folio existe en la base de datos
            fetch(`check_folio.php?folio=${folio}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        const url = `print_pallets.php?pallets=${selectedPallets.join(',')}&folio=${folio}`;
                        window.open(url, '_blank');
                    } else {
                        alert('El número de folio ingresado no existe. Por favor, ingrese un número de folio válido.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al verificar el número de folio.');
                });
        }
    });

     // Asociar eventos a los botones de eliminar item
     document.querySelectorAll('.btn-delete-item').forEach(button => {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-item-id');
            if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                fetch(`delete_item.php?id=${itemId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`tr[data-item-id="${itemId}"]`).remove();
                            alert(data.message);
                        } else {
                            alert(data.message || 'Error al eliminar el registro.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el registro.');
                    });
            }
        });
    });

    // Asociar eventos a los botones de mostrar formulario de agregar item
    document.querySelectorAll('.btn-show-add-item').forEach(button => {
        button.addEventListener('click', function () {
            const palletId = this.getAttribute('data-pallet-id');
            const folioId = this.getAttribute('data-folio-id');
            const url = `add_item_form.php?pallet_id=${palletId}&folio_id=${folioId}`;
            window.open(url, 'Agregar Item', 'width=600,height=400');
        });
    });

    // Asociar eventos a los botones de eliminar pallet
    document.querySelectorAll('.btn-delete-pallet').forEach(button => {
        button.addEventListener('click', function () {
            const palletId = this.getAttribute('data-pallet-id');
            if (confirm('¿Estás seguro de que deseas eliminar este pallet?')) {
                fetch(`delete_pallet.php?id=${palletId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`.pallet[data-pallet-id="${palletId}"]`).remove();
                            alert(data.message);
                        } else {
                            alert(data.message || 'Error al eliminar el pallet.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el pallet.');
                    });
            }
        });
    });


    // Asociar eventos a los botones de editar item
    document.querySelectorAll('.btn-edit-item').forEach(button => {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-item-id');
            const url = `edit_item.php?id=${itemId}`;
            window.location.href = url;
        });
    });

    // Asociar eventos a los botones de eliminar folio
    document.querySelectorAll('.btn-delete-folio').forEach(button => {
        button.addEventListener('click', function () {
            const folioId = this.getAttribute('data-folio-id');
            if (confirm('¿Estás seguro de que deseas eliminar este folio?')) {
                fetch(`delete_folio.php?id=${folioId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`.accordion-item[data-folio-id="${folioId}"]`).remove();
                            alert(data.message);
                        } else {
                            console.error('Error al eliminar el folio:', data);
                            alert(data.message || 'Error al eliminar el folio.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el folio.');
                    });
            }
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
