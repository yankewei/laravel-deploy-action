@servers(['web' => '8.210.56.133'])

@task('deploy')
    cd /path/to/site
    git pull origin master
@endtask