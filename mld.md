# MLD — Modèle Logique de Données

## Tables

```
ROLE (id PK, nomRole UNIQUE, description)

EMPLOYE (id PK, nom, prenom, email UNIQUE, telephone, dateEmbauche, statut, password)

EMPLOYE_ROLE (employe_id FK→EMPLOYE.id, role_id FK→ROLE.id)
  PK (employe_id, role_id)

PATIENT (id PK, nom, prenom, dateNaissance, sexe, adresse, telephone, email,
         numeroSecuriteSociale UNIQUE)

ETAGE (id PK, numeroEtage, nomEtage, description, dateCreation)

CHAMBRE (id PK, etage_id FK→ETAGE.id, numeroChambre, typeChambre, statut, description)

LIT (id PK, chambre_id FK→CHAMBRE.id, numeroLit, statut, description)

RESERVATION (id PK, lit_id FK→LIT.id, patient_id FK→PATIENT.id,
             employe_id FK→EMPLOYE.id, dateDebut, dateFin, statut, commentaire)

LOG (id PK, employe_id FK→EMPLOYE.id (nullable), action, tableConcernee,
     idObjet, ancienEtat, nouvelEtat, dateAction)
```

## Relations

| Table | Type | Cible |
|---|---|---|
| EMPLOYE_ROLE | ManyToMany | EMPLOYE ↔ ROLE |
| CHAMBRE.etage_id | ManyToOne | → ETAGE |
| LIT.chambre_id | ManyToOne | → CHAMBRE |
| RESERVATION.lit_id | ManyToOne | → LIT |
| RESERVATION.patient_id | ManyToOne | → PATIENT |
| RESERVATION.employe_id | ManyToOne | → EMPLOYE |
| LOG.employe_id | ManyToOne nullable | → EMPLOYE |

## Hiérarchie physique

```
ETAGE
 └── CHAMBRE (n chambres par étage)
      └── LIT (n lits par chambre)
           └── RESERVATION (n réservations par lit)
                ├── PATIENT
                └── EMPLOYE ──< EMPLOYE_ROLE >── ROLE

LOG (audit global, lié optionnellement à un EMPLOYE)
```
