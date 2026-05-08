<?php
// Lista de iconos FontAwesome categorizados
function getIcons() {
    return [
        'Negocio' => [
            'fa-file-invoice' => '📄 Factura / Nómina',
            'fa-file-contract' => '📝 Contrato',
            'fa-briefcase' => '💼 Maletín',
            'fa-building' => '🏢 Edificio',
            'fa-calculator' => ' Calculadora'
        ],
        'Finanzas' => [
            'fa-euro-sign' => '€ Euro',
            'fa-dollar-sign' => '$ Dólar',
            'fa-chart-line' => '📈 Gráfico Ascendente',
            'fa-chart-pie' => '🥧 Gráfico Circular',
            'fa-wallet' => '👛 Cartera',
            'fa-piggy-bank' => '🐷 Hucha'
        ],
        'Animales/Naturaleza' => [
            'fa-crow' => '🐦 Pájaro/Gallina',
            'fa-dove' => '🕊️ Paloma',
            'fa-paw' => '🐾 Huella',
            'fa-leaf' => '🍃 Hoja',
            'fa-tree' => '🌳 Árbol'
        ],
        'Tecnología' => [
            'fa-laptop' => '💻 Portátil',
            'fa-mobile-alt' => '📱 Móvil',
            'fa-server' => '🖥️ Servidor',
            'fa-cloud' => '☁️ Nube',
            'fa-wifi' => '📶 Wifi'
        ],
        'Usuarios' => [
            'fa-users' => '👥 Grupo',
            'fa-user-tie' => '👔 Ejecutivo',
            'fa-user-clock' => '⏳ Usuario Tiempo',
            'fa-headset' => '🎧 Soporte'
        ]
    ];
}
?>