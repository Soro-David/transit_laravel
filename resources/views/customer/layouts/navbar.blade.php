<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="{{route('home')}}" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link notification-icon" data-toggle="dropdown" href="#">
        <i class="fa fa-bell mx-5" aria-hidden="true"></i>
        <span class="badge badge-warning navbar-badge mx-5" id="notification-count">3</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header"> Notifications</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> Nouveau devis reçu
          <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-box mr-2"></i> Colis pris en charge
          <span class="float-right text-muted text-sm">2 hours</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">Voir toutes les notifications</a>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->

<script>
  document.addEventListener('DOMContentLoaded', function() {
  // Fonction pour mettre à jour les notifications
  function updateNotifications() {
    // Exemple de notifications (vous pouvez récupérer cela depuis votre backend)
    const notifications = [
      { message: 'Nouveau devis reçu', icon: 'fas fa-envelope', time: '3 mins' },
      { message: 'Colis pris en charge', icon: 'fas fa-box', time: '2 hours' }
    ];

    // Mise à jour du compteur de notifications
    document.getElementById('notification-count').textContent = notifications.length;

    // Mise à jour des éléments de la liste déroulante
    const dropdownMenu = document.querySelector('.dropdown-menu-right');
    dropdownMenu.innerHTML = `<span class="dropdown-item dropdown-header">${notifications.length} Notifications</span>`;
    notifications.forEach(notification => {
      const item = `
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="${notification.icon} mr-2"></i> ${notification.message}
          <span class="float-right text-muted text-sm">${notification.time}</span>
        </a>
      `;
      dropdownMenu.insertAdjacentHTML('beforeend', item);
    });
    dropdownMenu.insertAdjacentHTML('beforeend', '<div class="dropdown-divider"></div><a href="#" class="dropdown-item dropdown-footer">Voir toutes les notifications</a>');
  }

  // Appel initial pour charger les notifications
  updateNotifications();

  // Simuler la réception de nouvelles notifications toutes les 10 secondes
  setInterval(updateNotifications, 10000);
});

</script>

<style>
  .notification-icon i {
  font-size: 24px; /* Augmente la taille de l'icône */
  color: #ff4500; /* Couleur orange vif pour attirer l'attention */
}

.navbar-badge {
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
  background-color: #ff4500; /* Couleur de fond du badge */
  color: white;
  font-size: 12px; /* Taille du texte du badge */
  padding: 2px 6px;
  border-radius: 50%;
  border: 1px solid white; /* Bordure blanche autour du badge */
}

</style>