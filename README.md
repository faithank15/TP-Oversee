# Sujet C: Application de suivi de stock d'une petite boutique. (Oversee)

**Nom choisi pour l'application** : Oversee ("Superviser" en français)

## Équipe 3
**Membres** :
  - DOSSA Sèdjro Marc Gaby 
  - KAGBAHINTO Oswald Faith Mahougnon 

### Fonctionnalités

- Création de compte, connexion et déconnexion.
- Gestion des produits : ajout, consultation, modification et suppression.
- Gestion des catégories de produits.
- Enregistrement des entrées, sorties et ajustements de stock.
- Consultation et filtrage de l'historique des mouvements.
- Recherche et filtrage des produits.
- Alertes de stock faible et de rupture.
- Tableau de bord avec indicateurs sur le stock.
- Gestion indépendante des espaces de plusieurs boutiques.

### Choix techniques

| Technologie |	Utilisation |
| :--- | :---|
| Laravel 12 |	Framework backend (MVC) |
| PHP 8.2+ |	Langage backend |
| MySQL / MariaDB |	Base de données |
| Laravel Fortify |	Authentification |
| Blade |	Moteur de templates |
| Tailwind CSS |	Stylisation de l'interface |
| Vite |	Compilation des ressources frontend |
| Node.js / npm |	Gestion des dépendances frontend |

### Installation et lancement

```bash
# Cloner le dépôt
git clone https://github.com/faithank15/TP-Oversee.git
cd TP-Oversee

# Installer les dépendances
composer install
npm install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate
```

Créer ensuite une base de données MySQL nommée oversee, puis configurer le fichier .env :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oversee
DB_USERNAME=root
DB_PASSWORD=
```

Enfin :

```bash
# Créer les tables et les données de démonstration
php artisan migrate --seed

# Compiler les ressources frontend
npm run build

# Démarrer le serveur
npm run dev
php artisan serve
```

L'application sera accessible à l'adresse http://127.0.0.1:8000.
