/*
* npm init (инициализация проекта)
* npm install --global gulp (выполнить, если не установлен глобально)
* npm link gulp (линк в глобальный)
* */
const gulpfile = require('gulp');
const shell = require('gulp-shell');

gulpfile.task('up', () => {
    return gulpfile.src('/').pipe(shell([
            'python checkup.py'
        ],
        {cwd: __dirname+'/bxbuild'}));
});
gulpfile.task('deploy', () => {
    return gulpfile.src('/').pipe(shell([
			'python cp1251.py',
			'python updater.py',
            'python cl.py',
            'python send.py'
        ],
        {cwd: __dirname+'/bxbuild'}));
});
gulpfile.task('lang', () => {
    return gulpfile.src('/').pipe(shell([
            'python lang.py'
        ],
        {cwd: __dirname+'/tests'}));
});
gulpfile.task('lang-d', () => {
    return gulpfile.src('/').pipe(shell([
            'python lang.py --dep'
        ],
        {cwd: __dirname+'/tests'}));
});

function defaultTask(cb) {
    console.log('gulp lang', '-', 'тест языковых');
    console.log('gulp up', '-', 'сборка обновления');
    console.log('gulp deploy', '-', 'публикация обновления в маркетплейс');
    cb();
}


exports.default = defaultTask