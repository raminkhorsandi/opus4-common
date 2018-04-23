#!groovy

pipeline {
    agent any

    stages {
        stage('clean') {
            steps {
                sh 'rm -rf build/'
                sh 'mkdir build/'
                sh 'mkdir build/results/'
                sh 'mkdir build/checkstyle/'
                sh 'mkdir build/coverage/'
            }
        }

        stage('prepare') {
            steps {
                sh 'composer install'
                sh 'composer update'
            }
        }

        stage('test') {
            steps {
                sh 'composer check-coverage'
            }
        }

        stage('publish') {
            steps {
                step([
                    $class: 'JUnitResultArchiver',
                    testResults: 'build/results/phpunit.xml'
                ])
                step([
                    $class: 'hudson.plugins.checkstyle.CheckStylePublisher',
                    pattern: 'build/checkstyle/checkstyle.xml'
                ])
                step([
                    $class: 'CloverPublisher',
                    cloverReportDir: 'build',
                    cloverReportFileName: 'build/coverage/clover.xml'
                ])
            }
        }
    }
}