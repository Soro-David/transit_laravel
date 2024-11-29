<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="{{ route('home') }}" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link notification-icon" data-toggle="dropdown" href="#">
        <i class="fa fa-bell mx-5" aria-hidden="true"></i>
        <span class="badge badge-warning navbar-badge mx-5" id="notification-count">3</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">3 Notifications</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> Nouveau devis reçu
          <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-file-alt mr-2"></i> Prestation validée
          <span class="float-right text-muted text-sm">1 hour</span>
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

    <li class="nav-item">
      <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
        <i class="nav-icon fas fa-power-off"></i>
        <p>{{ __('trans.logout') }}</p>
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
          @csrf
        </form>
      </a>
    </li>
  </ul>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour mettre à jour les notifications
    function updateNotifications() {
      const notifications = [
        { message: 'Nouveau devis reçu', icon: 'fas fa-envelope', time: '3 mins' },
        { message: 'Prestation validée', icon: 'fas fa-file-alt', time: '1 hour' },
        { message: 'Colis pris en charge', icon: 'fas fa-box', time: '2 hours' }
      ];

      document.getElementById('notification-count').textContent = notifications.length;

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

    updateNotifications();
    setInterval(updateNotifications, 10000);
  });
</script>

<style>
  .notification-icon i {
    font-size: 24px;
    color: #ff4500;
  }

  .navbar-badge {
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(50%, -50%);
    background-color: #ff4500;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50%;
    border: 1px solid white;
    
  }
  .main-header.navbar {
    height: 70px;
    line-height: 10px;
}





  body {
    padding-top: 50px;
  }
  .power-off-icon {
    font-size: 30px;  
    color: #dc3545;   
    margin-left: 10px; 
    display: inline-block;
    vertical-align: middle; 
    transition: transform 0.3s, color 0.3s; 
  }

  .power-off-icon:hover {
    transform: scale(1.2); 
    color: #c82333; 
  }


</style>
