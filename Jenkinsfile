#!groovy

pipeline {
    agent any

    stages {
        stage('prepare') {
            steps {
                sh 'composer install'
                sh 'composer update'
            }
        }

        stage('build') {
            steps {
                sh 'composer check'
            }
        }

        stage('publish') {
            steps {
                echo 'TODO publish test results'
                echo 'TODO publish checkstyle result'
                echo 'TODO publish coverage'
            }
        }
    }
}