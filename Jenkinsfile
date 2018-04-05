#!groovy

pipeline {
    agent any

    stages {
        stage('prepare') {
            composer install
            composer update
        }

        stage('build') {
            steps {
                composer check
            }
        }
    }
}