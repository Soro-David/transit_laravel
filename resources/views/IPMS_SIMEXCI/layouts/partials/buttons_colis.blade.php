<button class="btn btn-success pay-btn"
        data-reference             ="{{ $reference }}"
        data-colis-ids             ='@json($colisIds)'
        data-total-fcfa-text       ="{{ $totalFcfaText }}"
        data-paid-fcfa-text        ="{{ $paidFcfaText }}"
        data-remaining-fcfa-text   ="{{ $remainingFcfaText }}">
  <i class="fa fa-dollar-sign"></i>
</button>
