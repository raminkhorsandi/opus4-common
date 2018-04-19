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
                step([$class: 'hudson.plugins.checkstyle.CheckStylePublisher', pattern: 'build/logs/checkstyle.xml'])
                step([$class: 'hudson.plugins.clover.CloverPublisher', pattern: 'build/logs/phpunit.coverage.xml'])
            }
        }
    }
}