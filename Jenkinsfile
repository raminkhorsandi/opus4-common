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
                sh 'curl -s http://getcomposer.org/installer | php && php composer.phar self-update && php composer.phar install'
                sh 'sudo apt-get update'
                sh 'pecl install xdebug-2.8.0 && echo "zend_extension=/usr/lib/php/20151012/xdebug.so" >> /etc/php/7.0/cli/php.ini'
            }
        }

        stage('test') {
            steps {
                script{
                    switch (buildType) {
                        case "long":
                            sh 'php composer.phar check-full'
                            break
                        default:
                            sh 'php composer.phar test'
                            break
                    }
                }
            }
        }
    }
    post {
        always {
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
                cloverReportDir: 'build',
                cloverReportFileName: 'clover.xml'
            ])
            step([
                $class: 'hudson.plugins.pmd.PmdPublisher',
                pattern: 'build/phpmd.xml'
            ])
            step([
                $class: 'SloccountPublisher',
                pattern: 'build/phploc.csv'
            ])
            sh "chmod -R 777 ."
            step([$class: 'WsCleanup', externalDelete: 'rm -rf *'])
        }
    }
}
