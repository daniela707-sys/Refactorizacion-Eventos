<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @keyframes slideInFromTop {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideOutToTop {
        from {
            opacity: 1;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            transform: translateY(-30px);
        }
    }

    .custom-popup {
        animation: slideInFromTop 0.5s ease-out forwards;
    }

    .modal-body-product-details p {
        text-align: left;
    }

    .custom-popup.swal2-hide {
        animation: slideOutToTop 0.5s ease-in forwards;
    }

    .custom-modal-global {
        width: 100%;
    }

    .custon-modal-global-action {
        display: none;
        visibility: hidden;
    }

    div:where(.swal2-container).swal2-center>.swal2-popup {
        width: 60%;
        padding: 3% 0px;
        max-height: 80%;
    }

    div:where(.swal2-container).swal2-backdrop-show,
    div:where(.swal2-container).swal2-noanimation {
        background: #1c4b0826;
    }

    .swal2-actions.custon-modal-global-action {
        visibility: hidden;
    }

    .modal-content-product-details {
        background-color: var(--ogenix-white);
        width: 100%;
        height: 100%;
    }

    .close-modal-product-details {
        color: var(--ogenix-gray);
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .close-modal-product-details:hover,
    .close-modal-product-details:focus {
        color: var(--ogenix-black);
    }

    .modal-body-product-details {
        padding: 20px 0;
    }

    .name-product-modal {
        font-family: var(--ogenix-font-two);
        color: var(--ogenix-base);
        margin-top: 0;
        font-size: 2.5em;
        letter-spacing: var(--ogenix-letter-spacing);
    }

    .modal-body-product-details img {
        display: block;
        margin: 0 auto 20px;
        border-radius: var(--ogenix-bdr-radius);
        max-width: 100%;
        height: auto;
    }

    .modal-body-product-details p {
        color: var(--ogenix-gray);
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .price {
        font-size: 1.5em;
        color: var(--ogenix-base);
        font-weight: bold;
        margin-bottom: 20px;
    }

    .left-content-product,
    .right-content-product {
        width: 50%;
        display: flex;
        justify-content: center;

    }

    .left-content-product {
        align-items: center;
    }

    .right-content-product {
        align-items: flex-start;
        flex-direction: column;
        text-align: left;
        gap: 10px;
    }


    .contaner-modal-product {
        display: flex;
        flex-direction: row;
        gap: 10px;
        width: 100%;
    }

    .img-modal-product {
        border-radius: 10px;
        height: 50%;
        max-height: 500px;
        max-width: 500px;
        object-fit: contain;
        text-align: left;
    }

    @media only screen and (max-width:768px) {
        div:where(.swal2-container).swal2-center>.swal2-popup {
            width: 95%;
            height: 50vh;
            max-height: 70vh;
        }

        .contaner-modal-product {
            flex-direction: column;
        }

        .left-content-product,
        .right-content-product {
            flex-direction: column;
            gap: 10px;
            width: 100%;
            text-align: justify;
        }

        .name-product-modal {
            font-size: 1.5em;
        }

        .right-content-product p {
            font-size: 1em;
        }


        .img-modal-product {
            border-radius: 10px;
            height: 120px;
            max-height: 500px;
            object-fit: contain;
            text-align: left;
        }

    }
</style>



<script>
    // Function to open the modal and populate it with product details
    function openProductModal(name, urlimg, description, price, link) {
        var content = `<h2 class="name-product-modal" style="" class="price">${name}</h2>
        <p>${description}</p>`
        if (price) {
            content += `<p class="price">Precio:$ ${price}</p>`;
        }
        // Agregar el enlace de detalles
        content += `<a href="${link}" class="thm-btn main-menu__btn">Más detalles</a>`;
        Swal.fire({
            html: `
<div id="productDetailsModal" class="modal-product-details">
    <div class="modal-content-product-details">
        <div class="contaner-modal-product" class="modal-body-product-details">
            <div class="left-content-product">  <img class="img-modal-product" src="${urlimg}" alt="${name}"></div>
            <div class="right-content-product"> ${content}</div>
        </div>
    </div>
</div>
  `,
            customClass: {
                container: 'custom-modal-global',
                actions: 'custon-modal-global-action',
                popup: 'custom-popup'
            },
            showClass: {
                popup: ''
            },
            hideClass: {
                popup: 'swal2-hide' // Usa la animación de salida personalizada
            },
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: false,
        });

        var productDetailsModal = document.getElementById('productDetailsModal');
        var closeModalButton = document.querySelector('.close-modal-product-details');
        var modalBody = productDetailsModal.querySelector('.modal-body-product-details');
        productDetailsModal.style.display = 'block';

        var elemento = document.querySelector('.swal2-actions.custon-modal-global-action');
        if (elemento) {
            elemento.remove();
        }
    }

    function closeProductModal() {
        var productDetailsModal = document.getElementById('productDetailsModal');
        productDetailsModal.style.display = 'none';
        document.body.classList.remove('no-scroll');
    }

    // Close modal

    closeModalButton.onclick = function() {
        var productDetailsModal = document.getElementById('productDetailsModal');
        var closeModalButton = document.querySelector('.close-modal-product-details');
        productDetailsModal.style.display = 'none';
        document.body.classList.remove('no-scroll');
    }

    window.onclick = function(event) {
        var productDetailsModal = document.getElementById('productDetailsModal');
        var closeModalButton = document.querySelector('.close-modal-product-details');
        if (event.target === productDetailsModal) {
            productDetailsModal.style.display = 'none';
            document.body.classList.remove('no-scroll');
        }
    }
</script>
