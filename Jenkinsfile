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
                sh 'composer check-cov'
            }
        }

        stage('publish') {
            steps {
                step([$class: 'hudson.plugins.qtest.QtestPublisher', pattern: 'build/phpunit.xml'])
                step([$class: 'hudson.plugins.checkstyle.CheckStylePublisher', pattern: 'build/checkstyle.xml'])
                step([$class: 'CloverPublisher', cloverReportDir: 'build', cloverReportFileName: 'clover.xml'])
            }
        }
    }
}