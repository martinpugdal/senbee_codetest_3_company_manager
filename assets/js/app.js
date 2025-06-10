document.getElementById('companyForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const cvr = document.getElementById('cvr').value;

    const response = await fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({action: 'create', cvr})
    });

    const result = await response.json();

    if (result.success) {
        document.getElementById('companyForm').reset();
        await addCompanyToList(result.data)
        alert('Virksomhed oprettet!');
    } else {
        alert('Fejl: ' + result.error);
    }
});

let allCompanies = [];

async function loadCompanies() {
    const response = await fetch('index.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'fetch'})
    });

    allCompanies = await response.json();
    renderCompanyList();
}

function renderCompanyList() {
    const search = document.getElementById('companySearch').value.toLowerCase();
    const list = document.getElementById('companyList');
    list.innerHTML = '';

    allCompanies
        .filter(company => (company.name || '').toLowerCase().includes(search) || (company.address || '').toLowerCase().includes(search) || (company.cvr || '').toLowerCase().includes(search))
        .forEach(company => {
            const item = document.createElement('li');
            item.textContent = company.name + ' - ' + (company.address || 'Ukendt adresse');

            const syncBtn = document.createElement('button');
            syncBtn.className = 'sync-btn';
            syncBtn.textContent = 'Synkroniser';
            syncBtn.onclick = async function () {
                await syncCompany(company.cvr, item);
            };
            item.appendChild(syncBtn);

            const delBtn = document.createElement('button');
            delBtn.className = 'delete-btn';
            delBtn.textContent = 'Slet';
            delBtn.onclick = async function () {
                await deleteCompany(company.cvr);
            };
            item.appendChild(delBtn);

            list.appendChild(item);
        });
}

document.getElementById('companySearch').addEventListener('input', renderCompanyList);

document.addEventListener('DOMContentLoaded', async function () {
    await loadCompanies();
});

async function addCompanyToList(company) {
    const list = document.getElementById('companyList');
    const item = document.createElement('li');
    item.textContent = company.name + ' - ' + (company.address || 'Ukendt adresse');
    list.appendChild(item);
}

async function syncCompany(cvr, item) {
    const response = await fetch('index.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'sync', cvr})
    });
    const result = await response.json();
    if (result.success) {
        const companyIndex = allCompanies.findIndex(company => company.cvr === cvr);
        if (companyIndex !== -1) {
            allCompanies[companyIndex] = result.data;
        } else {
            // just in case? But should not happen unless someone else removed the company
            allCompanies.push(result.data);
        }

        item.textContent = result.data.name + ' - ' + (result.data.address || 'Ukendt adresse');
        alert('Data synced!');
    } else {
        alert('Sync failed: ' + result.error);
    }
}

async function deleteCompany(cvr) {
    const response = await fetch('index.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'delete', cvr})
    });
    const result = await response.json();
    if (result.success) {
        allCompanies = allCompanies.filter(company => company.cvr !== cvr);
        renderCompanyList();
        alert('Virksomhed slettet!');
    } else {
        alert('Sletning mislykkedes: ' + result.error);
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    await loadCompanies();
});