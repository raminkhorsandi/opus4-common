#!groovy

pipeline {
    agent any

    stages {
        stage('clean') {
            steps {
                sh 'rm -rf build/'
                sh 'mkdir build/'
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
                sh 'composer check-full'
            }
        }

        stage('publish') {
            steps {
                step([
                    $class: 'JUnitResultArchiver',
                    testResults: 'build/phpunit.xml'
                ])
                step([
                    $class: 'hudson.plugins.checkstyle.CheckStylePublisher',
                    pattern: 'build/checkstyle.xml'
                ])
                step([
                    $class: 'CloverPublisher',
                    cloverReportDir: 'build/coverage',
                    cloverReportFileName: 'clover.xml'
                ])
            }
        }
    }
}
