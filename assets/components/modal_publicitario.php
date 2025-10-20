<!-- El Modal -->
<div id="container_modal" class="container_modal" onclick="closeModal()">
    <div id="Modal" class="modal">
        <div class="modal-content" onclick="event.stopPropagation()">
            <?php require_once 'offerofday.php' ?>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        var modal = document.getElementById("Modal");
        var container_modal = document.getElementById("container_modal");
        modal.style.display = "none";
        container_modal.style.display = "none";
        document.body.classList.remove("no-scroll");
    }

    /* Previene que el clic dentro del contenido del modal lo cierre
    document.querySelector(".modal-content").onclick = function(event) {
        event.stopPropagation();
    };*/

    // Agregar la clase no-scroll al cargar el modal
    document.body.classList.add("no-scroll");
</script>
