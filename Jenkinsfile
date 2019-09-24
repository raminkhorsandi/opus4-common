def jobNameParts = JOB_NAME.tokenize('/') as String[]
def projectName = jobNameParts[0]
def buildType = "short"

if (projectName.contains('night')) {
    buildType = "long"
}

pipeline {
    agent { dockerfile {args "-u root -v /var/run/docker.sock:/var/run/docker.sock"}}

    triggers {
        cron( buildType.equals('long') ? 'H 3 * * *' : '')
    }

    stages {

        stage('prepare') {
            steps {
                sh 'composer install'
                sh 'composer update'
            }
        }

        stage('test') {
            steps {
                script{
                    switch (buildType) {
                        case "long":
                            sh 'composer check-full'
                            break
                        default:
                            sh 'composer test'
                            break
                    }
                }
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
                    cloverReportDir: 'build/coverage/',
                    cloverReportFileName: 'clover.xml'
                ])
                step([$class: 'WsCleanup'])
            }
        }
    }
}
