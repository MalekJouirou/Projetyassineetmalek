var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('web/ressources/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js 
    .addEntry('main.min','./web/ressources/js/main.js')
   

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()

    // allow sass/scss files to be processed
     .enableSassLoader()
     
    //un fichier runtime va s'ajouter dans le builde (il f'aut linclure avant tous les scripts) 
    .enableSingleRuntimeChunk(0)
;

// export the final configuration
module.exports = Encore.getWebpackConfig();