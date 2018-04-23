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
                step([$class: 'JUnitResultArchiver', testResults: 'build/phpunit.xml'])
                step([$class: 'hudson.plugins.checkstyle.CheckStylePublisher', pattern: 'build/checkstyle.xml'])
                step([$class: 'CloverPublisher', cloverReportDir: 'build', cloverReportFileName: 'clover.xml'])
            }
        }
    }
}