const Encore = require('@symfony/webpack-encore');

Encore
    // Le répertoire où les fichiers compilés seront placés
    .setOutputPath('public/build/')
    // Le chemin public utilisé par le serveur web pour accéder aux fichiers compilés
    .setPublicPath('/build')
    // Le fichier d'entrée principal
    .addEntry('app', './assets/app.js')
    // Le fichier de style principal
    .addStyleEntry('styles', './assets/styles/app.scss')
    // Active le partage de runtime pour optimiser les fichiers JavaScript
    .enableSingleRuntimeChunk()
    // Nettoie le répertoire de sortie avant chaque construction
    .cleanupOutputBeforeBuild()
    // Active les notifications de construction
    .enableBuildNotifications()
    // Active les sourcemaps pour le développement
    .enableSourceMaps(!Encore.isProduction())
    // Active le versionnement des fichiers (cache-busting)
    .enableVersioning(Encore.isProduction())
    // Configure Babel pour utiliser la dernière version de JavaScript
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    // Active le chargement de Sass
    .enableSassLoader()
    // Active le chargement de PostCSS
    .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
