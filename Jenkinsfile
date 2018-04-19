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
                echo 'TODO publish test results'
                step([$class: 'hudson.plugins.checkstyle.CheckStylePublisher', pattern: 'build/checkstyle.xml'])
                step([$class: 'CloverPublisher', cloverReportDir: 'build', cloverReportFileName: 'clover.xml'])
                //step([$class: 'hudson.plugins.clover.CloverPublisher', pattern: 'build/clover.xml'])
            }
        }
    }
}