<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="{{ route('home') }}" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <!-- Notifications -->
<<<<<<< Updated upstream
    <li class="nav-item dropdown">
=======

    
    {{-- <li class="nav-item dropdown">
>>>>>>> Stashed changes
      <a class="nav-link notification-icon" data-toggle="dropdown" href="#">
          <i class="fas fa-bell fa-2x mx-5" aria-hidden="true"></i>
          <span class="badge badge-warning navbar-badge mx-5" id="notification-count">0</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notification-menu">
          <span class="dropdown-item dropdown-header" id="notification-header">0 Notifications</span>
          <div class="dropdown-divider"></div>
          <div id="notification-items"></div>
          <div class="dropdown-divider"></div>
          <a href="{{ route('notification.index') }}" class="dropdown-item dropdown-footer">Voir toutes les notifications</a>
      </div>
    </li>

    <!-- User Profile -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <img src="{{ Auth::user()->profile_photo_url ?? asset('images/poslg.png') }}" class="img-circle"  style="width: 30px; height: 30px;">
            <span>{{ auth()->user()->getFullname() }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#changeProfilePhotoModal">
          <i class="fas fa-user-circle mr-2"></i> Changer la photo de profil
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fa fa-edit"></i> Modifier le compte
        </a>
        <a href="#" class="dropdown-item" onclick="document.getElementById('logout-form').submit()">
          <i class="fas fa-sign-out-alt mr-2"></i> Se déconnecter
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>

<!-- Modal for Changing Profile Photo -->
<div class="modal fade" id="changeProfilePhotoModal" tabindex="-1" role="dialog" aria-labelledby="changeProfilePhotoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeProfilePhotoModalLabel">Changer la photo de profil</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="form-group">
            <label for="profile_photo">Télécharger une nouvelle photo</label>
            <input type="file" name="profile_photo" id="profile_photo" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
          <button type="submit" class="btn btn-primary">Sauvegarder</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- stylee  --}}
<style>
   body{
    padding-top: 50px;
    

  }
  .main-header{
    background-color: #ff9a03e8;
    color: #f09c1f;
  }
  .navbar .img-circle {
    border-radius: 50%;
    object-fit: cover;
  }
  .navbar-nav .dropdown-menu {
    min-width: 200px;
  }
  .navbar-nav .dropdown-menu .dropdown-item {
    font-size: 14px;
    padding: 10px;
  }
  .badge-danger {
    background-color: #ee0a21; /* Rouge Bootstrap */
    color: white;
}
.badge-warning {
    background-color: hsl(133, 76%, 18%); /* Rouge Bootstrap */
    color: white;
}
</style>

<script>
 $(document).ready(function () {
    // Fonction pour charger les notifications
    function loadNotifications() {
        $.ajax({
            url: '{{ route("agent_notification.get.notifications") }}',
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    // Mettre à jour le badge de notification
                    const notificationCount = response.count;
                    const badge = $('#notification-count');

                    badge.text(notificationCount);

                    // Vérifier si le nombre dépasse 0
                    if (notificationCount > 0) {
                        badge.removeClass('badge-warning').addClass('badge-danger');
                    } else {
                        badge.removeClass('badge-danger').addClass('badge-warning');
                    }

                    // Mettre à jour la liste des notifications
                    let notificationItems = '';
                    response.notifications.forEach(function (notification) {
                        notificationItems += `
                            <a href="#" class="dropdown-item notification-item" data-id="${notification.id}">
                                <i class="fas fa-box mr-2"></i> ${notification.message}
                                <span class="float-right text-muted text-sm">${notification.time}</span>
                            </a>
                            <div class="dropdown-divider"></div>
                        `;
                    });
                    $('#notification-items').html(notificationItems);

                    // Mettre à jour l'en-tête des notifications
                    $('#notification-header').text(`${notificationCount} Notifications`);
                }
            },
            error: function () {
                console.error('Erreur lors du chargement des notifications.');
            },
        });
    }

    // Charger les notifications au chargement de la page
    loadNotifications();

    // Recharger toutes les 30 secondes
    setInterval(loadNotifications, 30000);

    // Fonction pour marquer une notification comme lue et la retirer
    $(document).on('click', '.notification-item', function () {
        const notificationId = $(this).data('id');
        const clickedElement = $(this); // Conserver l'élément cliqué

        // Effectuer une requête AJAX pour marquer la notification comme lue
        $.ajax({
            url: '{{ route("agent_notification.markAsRead") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                notification_id: notificationId,
            },
            success: function (response) {
                if (response.success) {
                    // Retirer la notification du menu
                    clickedElement.closest('.notification-item').remove();

                    // Mettre à jour le compteur de notifications
                    const currentCount = parseInt($('#notification-count').text(), 10) || 0;
                    const newCount = Math.max(0, currentCount - 1);

                    $('#notification-count').text(newCount);

                    // Mettre à jour l'en-tête des notifications
                    if (newCount === 0) {
                        $('#notification-header').text('Aucune notification');
                        $('#notification-count').removeClass('badge-danger').addClass('badge-warning');
                    }
                }
            },
            error: function () {
                console.error('Erreur lors de la mise à jour de la notification.');
            },
        });
    });
});

</script>