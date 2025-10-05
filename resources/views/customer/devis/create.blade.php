@extends('customer.layouts.index')
@section('content-header')
@endsection

@section('content')
<section class="wizard-wrap p-4 mx-auto">
    <!-- Modal de succès pour création de devis -->
    @if(session('success_popup'))
    <div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Succès</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h5>{{ session('success_popup') }}</h5>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('customer_colis.devis.hold') }}" class="btn btn-success">OK</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
            
            // Redirection automatique après 2 secondes
            setTimeout(() => {
                window.location.href = "{{ route('customer_colis.devis.hold') }}";
            }, 2000);
        });
    </script>
    @endif

    <form action="{{ route('customer_colis.devis.store') }}" method="post" class="form-container" novalidate>
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

        {{-- Top card with progress (design like image1) --}}
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
                <div class="title-emoji">🚚</div>
                <h3>Informations Transport</h3>
            </div>
        </div>

        {{-- Fieldsets (steps) --}}
        <!-- Step 1 -->
        <fieldset class="step-fieldset">
            <div class="card-body form-section p-4 mb-3">
                <div class="row gx-3 gy-3 align-items-end">
                    <div class="col-md-3">
                        <label for="mode_transit" class="form-label">Mode de transit</label>
                        <select name="mode_transit" id="mode_transit" class="form-select">
                            <option value="" disabled selected>-- Choisir --</option>
                            <option value="maritime">Maritime</option>
                            <option value="aerien">Aérien</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="agence_destination_societe" class="form-label">Agence Destination</label>
                        <select name="agence_destination_societe" id="agence_destination_societe" class="form-select">
                            <option value="" disabled selected>-- Choisir --</option>
                            <option value="IPMS-SIMEX-CI">Carrefour Angré</option>
                            <option value="IPMS-SIMEX-CI Angre 8ème Tranche">Angré 8ème Tranche</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="pays_expedition" class="form-label">Pays d'expédition</label>
                        <select name="pays_expedition" id="pays_expedition" class="form-select">
                            <option value="" disabled selected>-- Choisir --</option>
                            @foreach ($paysUniques as $pays)
                                <option value="{{ $pays }}">{{ $pays }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="agence_expedition" class="form-label">Agence d'expédition</label>
                        <select name="agence_expedition" id="agence_expedition" class="form-select">
                            <option value="" disabled selected>-- Choisir --</option>
                            @foreach ($agencesExpedition as $agence)
                                <option value="{{ $agence->nom_agence }}"
                                        data-pays="{{ $agence->pays_agence ?? '' }}"
                                        data-devise="{{ $agence->devise ?? '' }}">
                                    {{ $agence->nom_agence }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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
                        <input type="text" name="nom_expediteur" id="nom_expediteur" value="{{ $user->first_name }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                        <input type="text" name="prenom_expediteur" id="prenom_expediteur" value="{{ $user->last_name }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label for="email_expediteur" class="form-label">Email</label>
                        <input type="email" name="email_expediteur" id="email_expediteur" value="{{ $user->email }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="tel_expediteur" class="form-label">Téléphone</label>
                        <input type="text" name="tel_expediteur" id="tel_expediteur" value="{{ $user->country_code_expediteur.' '.$user->tel }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="adresse_expediteur" class="form-label">Adresse</label>
                        <input type="text" name="adresse_expediteur" id="adresse_expediteur" value="{{ $user->adresse }}" class="form-control" required>
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

                <div class="row mb-3 gx-3">
                    <div class="col-md-3">
                        <label for="devise" class="form-label">Devise</label>
                        <select name="devise_visible" id="devise" class="form-select" required>
                            <option value="" disabled selected>-- Choisir --</option>
                            <option value="EUR">EUR</option>
                            <option value="FCFA">FCFA</option>
                        </select>
                    </div>
                </div>

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
                                <label class="form-label">Valeur colis</label>
                                <input type="number" name="valeur_colis[]" class="form-control prix-colis" placeholder="Valeur" disabled>
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
                    <button type="submit" class="btn btn-success">Valider</button>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="devise" id="devise_hidden" value="">

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

{{-- SCRIPTS (fusion complète : selects, toggle, clonage, navigation, fallbacks...) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------------- DOM references ----------------
    const form = document.querySelector('form.form-container');
    const paysSelect = document.getElementById('pays_expedition');
    const agenceSelect = document.getElementById('agence_expedition');
    const allAgenceOptions = agenceSelect ? Array.from(agenceSelect.querySelectorAll('option')) : [];
    const deviseSelect = document.getElementById('devise');
    const deviseHidden = document.getElementById('devise_hidden');
    const modeTransitSelect = document.getElementById('mode_transit');
    const modeSelect = document.getElementById('mode_transit');
    const agenceDestSelect = document.querySelector('select[name="agence_destination_societe"]');
    // ---------- Helper: rebuild agence options based on pays ----------
    function rebuildAgenceOptions(selectedPays) {
        if (!agenceSelect) return;
        const current = agenceSelect.value;
        agenceSelect.innerHTML = '';
        let foundCurrent = false;

        // include placeholder if none
        const filtered = allAgenceOptions.filter(opt => {
            const optPays = (opt.getAttribute('data-pays') || '').toString();
            if (!selectedPays || selectedPays === '') return true;
            return optPays === selectedPays;
        });

        if (filtered.length === 0) {
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.selected = true;
            placeholder.textContent = '-- Aucune agence disponible --';
            agenceSelect.appendChild(placeholder);
            return;
        }

        filtered.forEach(opt => {
            const clone = opt.cloneNode(true);
            agenceSelect.appendChild(clone);
            if (clone.value === current) foundCurrent = true;
        });

        if (foundCurrent) agenceSelect.value = current;
        else agenceSelect.selectedIndex = 0;
    }

    if (paysSelect) {
        paysSelect.addEventListener('change', function() {
            rebuildAgenceOptions(this.value);
        });
        if (paysSelect.value) rebuildAgenceOptions(paysSelect.value);
    }

    // ---------- Update devise from agence logic ----------
    function updateDeviseFromPays() {
    if (!paysSelect || !deviseSelect) return;
    const pays = paysSelect.value;

    if (pays === 'France') {
        deviseSelect.value = 'EUR';
        deviseSelect.disabled = true;
        deviseSelect.style.backgroundColor = '#f3f4f6';
    } else if (pays === 'Chine') {
        deviseSelect.value = 'FCFA';
        deviseSelect.disabled = true;
        deviseSelect.style.backgroundColor = '#f3f4f6';
    } else {
        deviseSelect.value = '';
        deviseSelect.disabled = true; // toujours auto, pas de choix manuel
        deviseSelect.style.backgroundColor = '#f3f4f6';
    }

    if (deviseHidden) deviseHidden.value = deviseSelect.value || '';
}
if (!modeSelect || !agenceDestSelect) return;

    const targetsByMode = {
        maritime: ['Carrefour Angré', 'Carrefour Angre', 'Carrefour-Angré'],
        aerien:  ['Angré 8ème Tranche', 'Angre 8ème Tranche', 'Angré 8eme Tranche']
    };

    function setAgenceByMode(mode) {
        const targets = targetsByMode[mode] || [];
        if (targets.length === 0) return;

        // 1) essayer un match exact sur value ou text
        for (const opt of agenceDestSelect.options) {
            if (targets.includes(opt.value) || targets.includes(opt.text)) {
                agenceDestSelect.value = opt.value;
                agenceDestSelect.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }

        // 2) essayer un match "contains" (insensible à la casse)
        for (const opt of agenceDestSelect.options) {
            const txt = (opt.text || '').toLowerCase();
            for (const t of targets) {
                if (txt.indexOf(t.toLowerCase()) !== -1) {
                    agenceDestSelect.value = opt.value;
                    agenceDestSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }
            }
        }
    }

    // écouteur sur le select mode_transit
    modeSelect.addEventListener('change', function() {
        setAgenceByMode(this.value);
    });

    // initialisation au chargement (si un mode déjà sélectionné)
    setAgenceByMode(modeSelect.value);
if (paysSelect) {
    paysSelect.addEventListener('change', updateDeviseFromPays);
    if (paysSelect.value) updateDeviseFromPays();
}

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
    if (modeTransitSelect) {
        modeTransitSelect.addEventListener('change', function() {
            toggleFields(this.value);
        });
        toggleFields(modeTransitSelect.value);
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

        // enable inputs/selects/textarea
        const inputs = colis.querySelectorAll('input, select, textarea, button');
        inputs.forEach(i => {
            i.removeAttribute('disabled');
            // if button classes present ensure visible/hide default handled later
        });

        // default quantity
        colis.querySelectorAll('input.quantite-colis').forEach(q => { if (!q.value) q.value = 1; });

        // Add proper event listeners (remove/add will be delegated)
        return colis;
    }

    function addInitialColisIfEmpty() {
        const container = document.getElementById('colisContainer');
        if (!container) return;
        if (container.querySelectorAll('.colis-fieldset').length === 0) {
            const first = createNewColis();
            if (!first) return;
            // hide remove on first, show add on it
            const rem = first.querySelector('.remove-colis'); if (rem) rem.style.display = 'none';
            const add = first.querySelector('.add-colis'); if (add) add.style.display = '';
            container.appendChild(first);
            attachDimensionListeners(first);
            toggleFields(modeTransitSelect ? modeTransitSelect.value : '');
        }
    }

    // Delegated click handlers for add/remove (works even for cloned nodes)
    document.addEventListener('click', function(e) {
        // add-colis
        if (e.target && e.target.matches('.add-colis')) {
            e.preventDefault();
            const currentBlock = e.target.closest('.colis-fieldset');
            if (currentBlock) {
                const thisAdd = currentBlock.querySelector('.add-colis');
                if (thisAdd) thisAdd.style.display = 'none';
            }
            const newColis = createNewColis();
            if (!newColis) return;
            // show remove on new
            const remBtn = newColis.querySelector('.remove-colis'); if (remBtn) remBtn.style.display = '';
            const addBtn = newColis.querySelector('.add-colis'); if (addBtn) addBtn.style.display = '';
            // enable buttons
            newColis.querySelectorAll('button').forEach(b => b.removeAttribute('disabled'));
            document.getElementById('colisContainer').appendChild(newColis);
            attachDimensionListeners(newColis);
            toggleFields(modeTransitSelect ? modeTransitSelect.value : '');
            if (deviseHidden && deviseSelect) deviseHidden.value = deviseSelect.value || '';
        }

        // remove-colis
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

    // initial
    addInitialColisIfEmpty();

    // ---------- Devise hidden sync & fallback before submit ----------
    if (deviseSelect && deviseHidden) {
        deviseHidden.value = deviseSelect.value || '';
        deviseSelect.addEventListener('change', function() {
            deviseHidden.value = this.value || '';
        });
    }

    if (form) {
        form.addEventListener('submit', function(ev) {
            // enable inputs inside container (in case some remained disabled)
            document.querySelectorAll('#colisContainer :input').forEach(i => i.removeAttribute('disabled'));

            // ensure devise hidden
            if (deviseHidden && !deviseHidden.value && deviseSelect) {
                deviseHidden.value = deviseSelect.value || '';
            }

            // fallback type_colis
            document.querySelectorAll('select[name="type_colis[]"]').forEach(s => { if (!s.value) s.value = 'standard'; });

            // fallback quantite
            document.querySelectorAll('input[name="quantite_colis[]"]').forEach(q => { if (!q.value) q.value = 1; });
        });
    }

    // ---------- Navigation multi-step (preserve your existing UX) ----------
    let currentStep = 0;
    const fieldsets = Array.from(document.querySelectorAll('.step-fieldset'));
    const stepItems = Array.from(document.querySelectorAll('.wizard-step'));
    const nextBtns = Array.from(document.querySelectorAll('.btn-next'));
    const prevBtns = Array.from(document.querySelectorAll('.btn-prev'));

    function showStep(step) {
        fieldsets.forEach((fs, idx) => fs.style.display = (idx === step) ? 'block' : 'none');
        stepItems.forEach((it, idx) => it.classList.toggle('active', idx <= step));
        // scroll a little into view for UX
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

    showStep(currentStep);

    // Attach dimension listeners for any initial elements
    attachDimensionListeners(document);

    // Observe colisContainer additions to re-apply listeners when necessary
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
});
</script>
@endsection