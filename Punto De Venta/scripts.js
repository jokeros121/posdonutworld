document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();

    document.getElementById('finalizar-venta').addEventListener('click', finalizarVenta);

    document.getElementById('buscar-btn').addEventListener('click', () => {
        const query = document.getElementById('buscar-producto').value;
        cargarProductos(query);
    });
});

let carrito = [];

function cargarProductos(query = '') {
    fetch(`buscar_productos.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const listaProductos = document.getElementById('lista-productos');
            listaProductos.innerHTML = '';

            data.forEach(producto => {
                const div = document.createElement('div');
                div.className = 'list-group-item';
                div.innerHTML = `
                <img src="imagenes/${producto.imagen}" alt="${producto.nombre}" class="img-thumbnail" style="width: 100px; height: 100px;">
                <span>${producto.nombre}</span>
                <span>$${producto.precio}</span>
                <span>Código: ${producto.codigo_barras}</span>
                <button class="btn btn-primary" onclick="agregarAlCarrito(${producto.id}, '${producto.nombre}', ${producto.precio}, '${producto.codigo_barras}')">Agregar</button>
                `;
                listaProductos.appendChild(div);
            });
        });
}

function agregarAlCarrito(id, nombre, precio, codigo) {
    const producto = carrito.find(p => p.id === id);
    if (producto) {
        producto.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1, codigo });
    }
    actualizarCarrito();
}

function actualizarCarrito() {
    const listaCarrito = document.getElementById('lista-carrito');
    listaCarrito.innerHTML = '';
    let total = 0;
    carrito.forEach(producto => {
        total += producto.precio * producto.cantidad;
        const div = document.createElement('div');
        div.className = 'list-group-item';
        div.innerHTML = `
            <span>${producto.nombre} (x${producto.cantidad})</span>
            <span>$${(producto.precio * producto.cantidad).toFixed(2)}</span>
        `;
        listaCarrito.appendChild(div);
    });
    document.getElementById('total').textContent = total.toFixed(2);
}

function finalizarVenta() {
    if (carrito.length === 0) {
        alert('El carrito está vacío.');
        return;
    }

    const nombreCliente = document.getElementById('nombre-cliente').value;
    const reseñaCliente = document.getElementById('reseña-cliente').value;
    if (nombreCliente.trim() === '') {
        alert('Por favor, ingrese el nombre del cliente.');
        return;
    }

    const venta = {
        total: document.getElementById('total').textContent,
        productos: carrito,
        nombre_cliente: nombreCliente,
        reseña_cliente: reseñaCliente
    };

    fetch('finalizar_venta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(venta)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `factura.php?id=${data.venta_id}`;
        } else {
            alert('Hubo un error al finalizar la venta.');
        }
    });
}
