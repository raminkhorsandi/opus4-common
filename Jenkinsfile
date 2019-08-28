def jobNameParts = JOB_NAME.tokenize('/') as String[]
def projectName = jobNameParts[0]

if (projectName.contains('night')) {
    TYPE = "long"
} else {
    TYPE = "short"
}

pipeline {
    agent { dockerfile {args "-u root -v /var/run/docker.sock:/var/run/docker.sock"}}

    triggers {
        cron( TYPE.equals('long') ? 'H 3 * * *' : '')
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
                    if (TYPE == 'short') {
                        sh 'composer test'
                    } else if (TYPE == 'long') {
                        sh 'composer check-full'
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
