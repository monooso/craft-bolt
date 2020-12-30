const tasks = arr => arr.join(' && ')

const fixer = './tools/vendor/bin/php-cs-fixer fix --config ./.php_cs ./src'
const cpd = './tools/vendor/bin/phpcpd --fuzzy ./src'
const phpunit = './tools/vendor/bin/phpunit'

module.exports = {
  'hooks': {
    'pre-commit': tasks(['lint-staged']),
    'pre-push': tasks([fixer, cpd, phpunit])
  }
}
