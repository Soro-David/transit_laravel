{{-- views/admin/transport/ajoutdevis.blade.php --}}
@extends('admin.layouts.admin')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Ajout du CSS pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<!-- Ajout de SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<section class="wizard-wrap p-4 mx-auto">
    <!-- SweetAlert 2 pour les notifications -->
    @if(session('success_popup'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: '{{ session('success_popup') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#05a805',
                background: '#fff',
                showClass: {
                    popup: 'animate__animated animate__bounceIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__bounceOut'
                },
                timer: 3000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    window.location.href = "{{ route('transport.ajout-devis') }}";
                }
            });

            setTimeout(() => {
                window.location.href = "{{ route('transport.ajout-devis') }}"
            }, 4000);
        });
    </script>
    @endif

    <form action="{{ route('transport.store.devis') }}" method="post" class="form-container" novalidate>
        @csrf

        {{-- Messages --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <h5 class="fw-bold mb-2">Oups ! Il y a eu des erreurs :</h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Top card with progress --}}
        <div class="wizard-card mb-4">
            <div class="wizard-top">
                <ul class="wizard-progress-list">
                    <li class="wizard-step active" data-step="0">
                        <div class="circle">1</div>
                        <div class="label">Transport</div>
                    </li>
                    <li class="wizard-step" data-step="1">
                        <div class="circle">2</div>
                        <div class="label">Client</div>
                    </li>
                    <li class="wizard-step" data-step="2">
                        <div class="circle">3</div>
                        <div class="label">Colis</div>
                    </li>
                </ul>
            </div>

            <div class="wizard-title text-center">
                <div class="title-emoji" id="wizard-emoji">🚚</div>
                <h3 id="wizard-title" class="animate__animated">Informations Transport</h3>
                <p id="wizard-subtitle" class="text-muted small mt-1">Remplis les informations pour commencer</p>
            </div>
        </div>

        {{-- Fieldsets (steps) --}}
        <!-- Step 1 -->
        <fieldset class="step-fieldset">
            <div class="card-body form-section p-4 mb-3">
                <div class="row gx-3 gy-3 align-items-end">
                    <div class="col-md-4">
                        <label for="mode_transit" class="form-label">Mode de transit</label>
                        <select name="mode_transit" id="mode_transit" class="form-select">
                            <option value="" disabled selected>-- Choisir --</option>
                            <option value="maritime" {{ old('mode_transit') == 'maritime' ? 'selected' : '' }}>Maritime</option>
                            <option value="aerien" {{ old('mode_transit') == 'aerien' ? 'selected' : '' }}>Aérien</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="agence_destination_societe" class="form-label">Agence D'expedition</label>
                        <select name="agence_destination_societe" id="agence_destination_societe" class="form-select">
                            <!-- options générées dynamiquement par JS -->
                            <option value="" disabled selected>-- Choisir --</option>
                            <option value="IPMS-SIMEX-CI">DS Translog Carrefour Angré</option>
                            <option value="IPMS-SIMEX-CI-ANGRE-8">DS Translog Angré 8ème Tranche</option>
                        </select>
                    </div>

                    {{-- Champs cachés pour les données fixes --}}
                    <input type="hidden" name="pays_expedition" value="France">
                    <input type="hidden" name="agence_expedition" value="AFT Agence Louis Bleriot">

                    <div class="col-12 text-end mt-3">
                        <button type="button" class="btn btn-primary btn-next">Suivant →</button>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Step 2 -->
        <fieldset class="step-fieldset" style="display:none;">
            <div class="card-body form-section p-4 mb-3">
                <h5 class="text-center mb-3">Informations sur le client</h5>
                <div class="row gx-3 gy-3">
                    <div class="col-md-6">
                        <label for="nom_expediteur" class="form-label">Nom</label>
                        <input type="text" name="nom_expediteur" id="nom_expediteur" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                        <input type="text" name="prenom_expediteur" id="prenom_expediteur" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label for="email_expediteur" class="form-label">Email</label>
                        <input type="email" name="email_expediteur" id="email_expediteur" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="tel_expediteur" class="form-label">Téléphone</label>
                        <input type="text" name="tel_expediteur" id="tel_expediteur" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="adresse_expediteur" class="form-label">Adresse</label>
                        <input type="text" name="adresse_expediteur" id="adresse_expediteur" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-prev">← Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant →</button>
            </div>
        </fieldset>

        <!-- Step 3 -->
        <fieldset class="step-fieldset" style="display:none;">
            <div class="card-body form-section p-4 mb-3">
                <h5 class="text-center mb-3">Informations sur le colis</h5>

                {{-- Template (invisible) --}}
                <div id="colisTemplate" style="display:none;">
                    <div class="colis-fieldset mb-4 border-top-1" style="padding-top:14px;">
                        <div class="row gx-2 gy-2">
                            <div class="col-md-2">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantite_colis[]" class="form-control quantite-colis" required disabled>
                            </div>

                            <div class="col-md-4 position-relative">
                                <label class="form-label">Produit(s)</label>
                                <input type="text" name="service[]" class="form-control produit-input" required disabled autocomplete="off">
                                <div class="autocomplete-results" style="position:absolute; z-index:1100; background:#fff; border:1px solid #eee; width:100%; display:none;"></div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Valeur colis (EUR)</label>
                                <input type="number" name="valeur_colis[]" class="form-control prix-colis" placeholder="Valeur en EUR" disabled>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Type colis</label>
                                <select name="type_colis[]" class="form-select" required disabled>
                                    <option value="standard" selected>Standard</option>
                                    <option value="fragile">Fragile</option>
                                </select>
                            </div>
                        </div>

                        <div class="row gx-2 gy-2 mt-2">
                            <div class="col-md-6 dimension-section">
                                <label class="form-label">Dimensions (cm)</label>
                                <div class="d-flex gap-2">
                                    <input type="number" name="longueur[]" class="form-control longueur" placeholder="L" disabled>
                                    <input type="number" name="largeur[]" class="form-control largeur" placeholder="l" disabled>
                                    <input type="number" name="hauteur[]" class="form-control hauteur" placeholder="h" disabled>
                                </div>
                                <div class="dimension-result mt-2" style="display:none; font-weight:600;"></div>
                            </div>

                            <div class="col-md-6 poids-section" style="display:none;">
                                <label class="form-label">Poids (kg)</label>
                                <input type="number" name="poids[]" class="form-control" placeholder="Poids" disabled>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description_colis[]" class="form-control" rows="3" disabled></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-2 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success add-colis" disabled>Ajouter un autre colis</button>
                            <button type="button" class="btn btn-danger remove-colis" style="display:none;" disabled>Retirer ce colis</button>
                        </div>
                    </div>
                </div>

                {{-- container --}}
                <div id="colisContainer"></div>

                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-secondary btn-prev">← Précédent</button>
                    <button type="button" id="btn-submit-devis" class="btn btn-success">Valider</button>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="devise" id="devise_hidden" value="EUR">

    </form>
</section>

{{-- STYLES --}}
<style>
    :root{
        --accent:#0b7cff;
        --accent-2:#05a805;
        --card-bg:#fff;
        --muted:#7b8694;
    }
    .wizard-wrap { max-width:1100px; margin:30px auto; }
    .wizard-card { background: linear-gradient(180deg,#fff 0,#fff 100%); border-radius:12px; padding:18px 22px; box-shadow:0 12px 40px rgba(22,28,37,0.06); }
    .wizard-top { padding:8px 0 0 0; }
    .wizard-progress-list { display:flex; gap:10px; align-items:center; justify-content:space-between; list-style:none; padding:24px 28px 4px; margin:0; position:relative; }
    .wizard-progress-list::before { content:""; position:absolute; left:8%; right:8%; top:38px; height:6px; background:#e9eef6; border-radius:6px; z-index:0; }
    .wizard-step { text-align:center; width:33%; z-index:2; cursor:pointer; }
    .wizard-step .circle { width:46px; height:46px; border-radius:50%; background:#e6e9ec; margin:0 auto; display:flex; align-items:center; justify-content:center; font-weight:700; color:#333; box-shadow:0 6px 18px rgba(11,124,255,0.06); transition:all .25s; }
    .wizard-step .label { margin-top:10px; font-size:13px; color:var(--muted); }
    .wizard-step.active .circle { background:var(--accent-2); color:#fff; transform:scale(1.05); box-shadow:0 10px 28px rgba(5,168,5,0.18); }
    .wizard-step.active .label { color:#222; font-weight:600; }
    .wizard-title { padding:14px 10px 4px; }
    .wizard-title h3 { margin:0; font-weight:600; color:#222; }
    .wizard-title .title-emoji { font-size:26px; margin-bottom:6px; }

    .form-container { background:var(--card-bg); border-radius:12px; padding:22px; box-shadow:0 8px 24px rgba(14,20,30,0.04); }
    .form-section { background:transparent; border-radius:8px; }

    .card-body { background:transparent; }

    .border-top-1 { border-top:1px solid #f0f0f0; }

    /* buttons */
    .btn { border-radius:8px; padding:8px 16px; }
    .btn-primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .btn-success { background:var(--accent-2); border-color:var(--accent-2); color:#fff; }
    .btn-secondary { background:#f3f5f7; border-color:#e9eef6; color:#222; }

    .dimension-result { color:#333; font-size:14px; }

    /* small responsive */
    @media (max-width:767px) {
        .wizard-progress-list::before { left:6%; right:6%; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------------- Variables globales ----------------
    let dernierDevisCree = null;
    let listeChauffeurs = [];

    // ---------------- DOM references ----------------
    const form = document.querySelector('form.form-container');
    const deviseHidden = document.getElementById('devise_hidden');
    const modeTransitSelect = document.getElementById('mode_transit');
    const agenceDestSelect = document.getElementById('agence_destination_societe');
    const btnSubmitDevis = document.getElementById('btn-submit-devis');
    const wizardTitle = document.getElementById('wizard-title');
    const wizardEmoji = document.getElementById('wizard-emoji');
    const wizardSubtitle = document.getElementById('wizard-subtitle');

    // ---------- Titres dynamiques par étape ----------
    const TITLES = [
        { title: 'Informations Transport', emoji: '🚚', subtitle: 'Mode de transit, agence et options de transport' },
        { title: 'Informations sur le client', emoji: '🧑‍🤝‍🧑', subtitle: 'Détails du client / expéditeur' },
        { title: 'Informations sur le colis', emoji: '📦', subtitle: 'Ajoutez les colis, dimensions et descriptions' }
    ];

    function updateWizardTitle(step) {
        const item = TITLES[step] || TITLES[0];
        // animation: ajouter puis retirer une classe pour relancer l'animation
        wizardTitle.classList.remove('animate__fadeIn');
        void wizardTitle.offsetWidth; // reflow pour relancer animation
        wizardEmoji.textContent = item.emoji;
        wizardTitle.textContent = item.title;
        wizardSubtitle.textContent = item.subtitle;
        wizardTitle.classList.add('animate__fadeIn');
    }

    // ---------- Devise fixée à EUR ----------
    function setDeviseToEUR() {
        if (deviseHidden) {
            deviseHidden.value = 'EUR';
        }
    }
    setDeviseToEUR();

    // ------------------- Agences par mode -------------------
    const agenceOptionsByMode = {
        maritime: [
            { value: 'IPMS-SIMEX-CI', label: 'DS Translog Carrefour Angré' }
        ],
        aerien: [
            { value: 'IPMS-SIMEX-CI-ANGRE-8', label: 'DS Translog Angré 8ème Tranche' }
        ]
    };

    /**
     * Remplit le select agence_destination_societe selon le mode fourni.
     * - si mode est 'maritime' : n'affiche que l'option maritime
     * - si mode est 'aerien' : n'affiche que l'option aerien
     * - si mode est falsy : restaure les deux options (placeholder + toutes)
     */
    function updateAgenceOptions(mode) {
        if (!agenceDestSelect) return;

        const previousValue = agenceDestSelect.value || '';

        // vide le select
        agenceDestSelect.innerHTML = '';

        // placeholder
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.disabled = true;
        placeholder.selected = true;
        placeholder.textContent = '-- Choisir --';
        agenceDestSelect.appendChild(placeholder);

        if (!mode) {
            // proposer toutes les options
            const all = [].concat(...Object.values(agenceOptionsByMode));
            all.forEach(opt => {
                const o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.label;
                if (previousValue && previousValue === opt.value) {
                    o.selected = true;
                }
                agenceDestSelect.appendChild(o);
            });
            agenceDestSelect.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }

        const list = agenceOptionsByMode[mode] || [];
        list.forEach(opt => {
            const o = document.createElement('option');
            o.value = opt.value;
            o.textContent = opt.label;
            agenceDestSelect.appendChild(o);
        });

        // restaurer si possible
        const restore = Array.from(agenceDestSelect.options).find(o => o.value === previousValue);
        if (restore) {
            agenceDestSelect.value = previousValue;
        } else {
            if (list.length > 0) agenceDestSelect.value = list[0].value;
        }

        agenceDestSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // écouteur sur changement de mode
    if (modeTransitSelect) {
        modeTransitSelect.addEventListener('change', function() {
            const mode = this.value;
            updateAgenceOptions(mode);
            toggleFields(mode);
        });
    }

    // appel initial (si old value côté serveur)
    const initialMode = modeTransitSelect ? modeTransitSelect.value : '';
    updateAgenceOptions(initialMode);

    // ---------- Toggle dimension/poids ----------
    function toggleFields(mode) {
        const containers = document.querySelectorAll('.colis-fieldset');
        containers.forEach(c => {
            const dim = c.querySelector('.dimension-section');
            const poids = c.querySelector('.poids-section');
            if (mode === 'maritime') {
                if (dim) dim.style.display = '';
                if (poids) poids.style.display = 'none';
            } else if (mode === 'aerien') {
                if (dim) dim.style.display = 'none';
                if (poids) poids.style.display = '';
            } else {
                if (dim) dim.style.display = '';
                if (poids) poids.style.display = 'none';
            }
        });
    }

    // ---------- Dimension listeners ----------
    function attachDimensionListeners(root) {
        const rootNode = (root && root.nodeType) ? root : document;
        const inputs = rootNode.querySelectorAll('.hauteur, .largeur, .longueur');
        inputs.forEach(inp => {
            inp.removeEventListener('input', dimensionListener);
            inp.addEventListener('input', dimensionListener);
        });

        function dimensionListener() {
            const parent = this.closest('.colis-fieldset') || this.closest('.dimension-section') || this.closest('.form-section');
            if (!parent) return;
            const h = parent.querySelector('.hauteur') ? parent.querySelector('.hauteur').value : '';
            const l = parent.querySelector('.largeur') ? parent.querySelector('.largeur').value : '';
            const L = parent.querySelector('.longueur') ? parent.querySelector('.longueur').value : '';
            const result = parent.querySelector('.dimension-result');
            if (h && l && L) {
                if (result) { result.textContent = `${L}x${l}x${h} cm`; result.style.display = ''; }
            } else {
                if (result) result.style.display = 'none';
            }
        }
    }

    // ---------- Clone colis template ----------
    function createNewColis() {
        const tpl = document.getElementById('colisTemplate');
        if (!tpl) return null;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = tpl.innerHTML.trim();
        const colis = wrapper.querySelector('.colis-fieldset');
        if (!colis) return null;

        const inputs = colis.querySelectorAll('input, select, textarea, button');
        inputs.forEach(i => {
            i.removeAttribute('disabled');
        });

        colis.querySelectorAll('input.quantite-colis').forEach(q => { if (!q.value) q.value = 1; });

        return colis;
    }

    function addInitialColisIfEmpty() {
        const container = document.getElementById('colisContainer');
        if (!container) return;
        if (container.querySelectorAll('.colis-fieldset').length === 0) {
            const first = createNewColis();
            if (!first) return;
            const rem = first.querySelector('.remove-colis'); if (rem) rem.style.display = 'none';
            const add = first.querySelector('.add-colis'); if (add) add.style.display = '';
            container.appendChild(first);
            attachDimensionListeners(first);
            toggleFields(modeTransitSelect ? modeTransitSelect.value : '');
        }
    }

    // Delegated click handlers for add/remove
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.add-colis')) {
            e.preventDefault();
            const currentBlock = e.target.closest('.colis-fieldset');
            if (currentBlock) {
                const thisAdd = currentBlock.querySelector('.add-colis');
                if (thisAdd) thisAdd.style.display = 'none';
            }
            const newColis = createNewColis();
            if (!newColis) return;
            const remBtn = newColis.querySelector('.remove-colis'); if (remBtn) remBtn.style.display = '';
            const addBtn = newColis.querySelector('.add-colis'); if (addBtn) addBtn.style.display = '';
            newColis.querySelectorAll('button').forEach(b => b.removeAttribute('disabled'));
            document.getElementById('colisContainer').appendChild(newColis);
            attachDimensionListeners(newColis);
            toggleFields(modeTransitSelect ? modeTransitSelect.value : '');
        }

        if (e.target && e.target.matches('.remove-colis')) {
            e.preventDefault();
            const block = e.target.closest('.colis-fieldset');
            if (!block) return;
            block.remove();
            const container = document.getElementById('colisContainer');
            if (container.querySelectorAll('.colis-fieldset').length === 0) {
                addInitialColisIfEmpty();
            } else {
                const last = container.querySelector('.colis-fieldset:last-child');
                if (last) {
                    const add = last.querySelector('.add-colis'); if (add) add.style.display = '';
                }
            }
        }
    });

    // ---------- Empêcher la soumission normale du formulaire ----------
    if (form) {
        form.addEventListener('submit', function(ev) {
            ev.preventDefault();
        });
    }

    // ---------- Gestion du bouton Valider ----------
    if (btnSubmitDevis) {
        btnSubmitDevis.addEventListener('click', function(e) {
            e.preventDefault();
            submitDevisForm();
        });
    }

    // ---------- Fonction pour soumettre le formulaire ----------
    function submitDevisForm() {
        document.querySelectorAll('#colisContainer input, #colisContainer select, #colisContainer textarea').forEach(i => i.removeAttribute('disabled'));
        document.querySelectorAll('select[name="type_colis[]"]').forEach(s => { if (!s.value) s.value = 'standard'; });
        document.querySelectorAll('input[name="quantite_colis[]"]').forEach(q => { if (!q.value) q.value = 1; });

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showConfirmationPopup(data.reference_generee);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Erreur lors de la création du devis',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la soumission',
                confirmButtonText: 'OK'
            });
        });
    }

    function showConfirmationPopup(referenceGeneree) {
        Swal.fire({
            title: '✅ Programme enregistrée avec succès !',
            html: `
                <div class="text-start">
                    <p class="mb-3">Référence du programme : <strong>${referenceGeneree}</strong></p>
                    <p class="mb-3">Le programme a été enregistré avec l'état <strong>"à planifié"</strong>.</p>
                    <p class="text-muted">Vous pourrez ultérieurement attribuer une date et un chauffeur.</p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Programmer maintenant',
            cancelButtonText: 'Plus tard',
            showCancelButton: true,
            confirmButtonColor: '#05a805',
        }).then((result) => {
            if (result.isConfirmed) {
                chargerChauffeurs().then(() => {
                    showProgrammationForm(referenceGeneree);
                });
            } else {
                window.location.href = "{{ route('transport.ajout-devis') }}";
            }
        });
    }

    async function chargerChauffeurs() {
        try {
            const response = await fetch("{{ route('transport.chauffeurs.list') }}");
            const data = await response.json();

            if (data.success) {
                listeChauffeurs = data.chauffeurs;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur chargement chauffeurs:', error);
            listeChauffeurs = [];
        }
    }

    function showProgrammationForm(referenceDevis) {
        const chauffeursOptions = listeChauffeurs.map(chauffeur =>
            `<option value="${chauffeur.id}">${chauffeur.full_name}</option>`
        ).join('');

        const today = new Date().toISOString().split('T')[0];
        const quantiteTotale = calculerQuantiteTotale();

        return Swal.fire({
            title: '📅 Programmer la récupération',
            html: `
                <form id="programmationForm">
                    <div class="mb-3">
                        <label class="form-label">Référence du programme</label>
                        <input type="text" class="form-control" value="${referenceDevis}" readonly>
                        <input type="hidden" name="reference_devis" value="${referenceDevis}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de récupération *</label>
                        <input type="date" name="date_programme" class="form-control" min="${today}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chauffeur *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Choisir un chauffeur --</option>
                            ${chauffeursOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantité totale *</label>
                        <input type="number" name="quantite" class="form-control" value="${quantiteTotale}" min="1" readonly>
                        <small class="form-text text-muted">Quantité calculée automatiquement à partir des colis</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nature du colis *</label>
                        <input type="text" name="nature_du_colis" class="form-control" value="Colis divers" required>
                    </div>
                </form>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: '✅ Programmer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#05a805',
            preConfirm: () => {
    const programmationForm = document.getElementById('programmationForm');
    const formData = new FormData(programmationForm);

    // On récupère et on ajoute explicitement certains champs qui viennent de la page principale
    const nomExp = document.querySelector('input[name="nom_expediteur"]').value || '';
    const prenomExp = document.querySelector('input[name="prenom_expediteur"]').value || '';
    const telExp = document.querySelector('input[name="tel_expediteur"]').value || '';
    const adresseExp = document.querySelector('input[name="adresse_expediteur"]').value || '';

    formData.set('nom_expediteur', `${nomExp} ${prenomExp}`.trim());
    formData.set('tel_expediteur', telExp);
    formData.set('lieu_expedition', adresseExp);

    // RÉPARATION IMPORTANTE : s'assurer que la nature du colis (saisie dans le popup) est bien envoyée
    const natureInput = programmationForm.querySelector('input[name="nature_du_colis"]');
    if (natureInput) {
        formData.set('nature_du_colis', natureInput.value || 'Colis divers');
    } else {
        // fallback: si popup n'a pas d'input (improbable), essayer sur la page principale
        const mainNature = document.querySelector('input[name="nature_du_colis"]');
        if (mainNature) formData.set('nature_du_colis', mainNature.value || 'Colis divers');
    }

    const url = "{{ route('transport.programmer.devis', ['reference' => ':reference']) }}".replace(':reference', referenceDevis);

    // DEBUG: log dans la console (temporarily pour vérifier)
    console.log('Programmation form payload preview:', {
        date_programme: formData.get('date_programme'),
        user_id: formData.get('user_id'),
        quantite: formData.get('quantite'),
        nature_du_colis: formData.get('nature_du_colis'),
    });

    return fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Erreur lors de la programmation');
        }
        return data;
    });
}
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '✅ Succès !',
                    html: `
                        <div class="text-start">
                            <p>Récupération programmée avec succès !</p>
                            <p><strong>Référence :</strong> ${result.value.programme.reference_generee}</p>
                            <p class="text-muted">Vous allez être redirigé...</p>
                        </div>
                    `,
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.href = "{{ route('transport.ajout-devis') }}";
                    }
                });
            }
        });
    }

    function calculerQuantiteTotale() {
        let quantiteTotale = 0;
        const champsQuantite = document.querySelectorAll('input[name="quantite_colis[]"]');
        champsQuantite.forEach(champ => {
            const quantite = parseInt(champ.value) || 0;
            quantiteTotale += quantite;
        });
        return quantiteTotale > 0 ? quantiteTotale : 1;
    }

    // ---------- Navigation multi-step ----------
    let currentStep = 0;
    const fieldsets = Array.from(document.querySelectorAll('.step-fieldset'));
    const stepItems = Array.from(document.querySelectorAll('.wizard-step'));
    const nextBtns = Array.from(document.querySelectorAll('.btn-next'));
    const prevBtns = Array.from(document.querySelectorAll('.btn-prev'));

    function showStep(step) {
        fieldsets.forEach((fs, idx) => fs.style.display = (idx === step) ? 'block' : 'none');
        stepItems.forEach((it, idx) => it.classList.toggle('active', idx <= step));
        updateWizardTitle(step);
        document.querySelector('.wizard-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    nextBtns.forEach(btn => btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentStep < fieldsets.length - 1) currentStep++;
        showStep(currentStep);
    }));
    prevBtns.forEach(btn => btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentStep > 0) currentStep--;
        showStep(currentStep);
    }));
    stepItems.forEach((it, idx) => it.addEventListener('click', function() {
        currentStep = idx;
        showStep(currentStep);
    }));

    // afficher l'étape initiale avec titre mis à jour
    showStep(currentStep);
    attachDimensionListeners(document);

    const colisContainer = document.getElementById('colisContainer');
    if (colisContainer) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (node.nodeType === 1 && node.matches('.colis-fieldset')) {
                        attachDimensionListeners(node);
                        toggleFields(modeTransitSelect ? modeTransitSelect.value : '');
                    }
                });
            });
        });
        observer.observe(colisContainer, { childList: true });
    }

    // Initialisation
    addInitialColisIfEmpty();
});
</script>
@endsection
