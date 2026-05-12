# Roadmap pour le Seeder "Full Simulation"

## Modifications selon vos spécifications

### Visibilité des décisions
- 90% publiques
- 10% privées
- Répartition aléatoire mais cohérente

### Pièces jointes  
- 90% publiques
- 10% privées
- La visibilité n'est pas forcément liées à la visibilité de la décision parente

### Types de cercles
- Répartition aléatoire mais équilibrée :
  - Open (50%)
  - Closed (30%)
  - Observateur open (20%)

### Hiérarchie des cercles
- 30% des cercles avec sous-cercles
- Logique :
  - Sous-cercles plus larges OU plus restreints que le parent
  - Héritage partiel des membres
  - Visibilité indépendante

## Structure mise à jour

### Décisions (200+)
- Visibilité :
  - Publique : 180
  - Privée : 20
- Phases :
  - Brouillon (15%) - majorité privée
  - Clarification (20%)
  - Réaction (30%)  
  - Objection (20%)
  - Adoptée/Abandonnée (15%)

### Pièces jointes
- 5-10 par décision (aléatoire)
- 90% publiques / 10% privées
- Types variés (PDF, images, docs)

### Cercles (10)
- Types :
  - 5 Open
  - 3 Closed  
  - 2 Observateur open
- 3 avec sous-cercles (1-2 niveaux)

## Validation supplémentaire
- Vérifier la cohérence visibilité décisions/pièces jointes
- Tester l'héritage des membres dans les sous-cercles
- Contrôler les règles d'accès par type de cercle