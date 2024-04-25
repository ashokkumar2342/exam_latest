<?php
Route::group(['middleware' => ['preventBackHistory','web']], function() {
	Route::get('login', 'Auth\LoginController@login')->name('admin.login'); 
	Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout.get');
	Route::get('logout_time', 'Auth\LoginController@logout')->name('admin.logout_time.get');
	Route::get('refreshcaptcha', 'Auth\LoginController@refreshCaptcha')->name('admin.refresh.captcha');
	Route::post('login-post', 'Auth\LoginController@loginPost')->name('admin.login.post');
});
Route::group(['middleware' => ['preventBackHistory','admin','web']], function() {
	Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
	Route::prefix('account')->group(function () {
	    Route::get('form', 'AccountController@form')->name('admin.account.form');
	    Route::post('store', 'AccountController@store')->name('admin.account.post');
		Route::get('list', 'AccountController@index')->name('admin.account.list');
		Route::get('edit/{account}', 'AccountController@edit')->name('admin.account.edit');
		Route::post('update/{account}', 'AccountController@update')->name('admin.account.edit.post');
		Route::get('delete/{account}', 'AccountController@destroy')->name('admin.account.delete'); 
		Route::get('status/{id}', 'AccountController@status')->name('admin.account.status');

		Route::get('change-password', 'AccountController@changePassword')->name('admin.account.change.password');
		Route::post('change-password-store', 'AccountController@changePasswordStore')->name('admin.account.change.password.store');
		Route::get('reset-password', 'AccountController@resetPassWord')->name('admin.account.reset.password'); 
		Route::post('reset-password-change', 'AccountController@resetPassWordChange')->name('admin.account.reset.password.change');		
	});

    Route::group(['prefix' => 'Master'], function() {
    	//onchange
    	Route::get('subjectwisesection', 'MasterController@subjectwisesection')->name('admin.Master.subjectwisesection');
    	//class
	    Route::get('class-index', 'MasterController@classIndex')->name('admin.Master.class.index');	   
	    Route::post('class-store/{id?}', 'MasterController@classStore')->name('admin.Master.class.store');   
	    Route::get('class-edit/{id}', 'MasterController@classEdit')->name('admin.Master.class.edit');	   
	    Route::get('class-delete/{id}', 'MasterController@classDelete')->name('admin.Master.class.delete');
	    //section
	    Route::get('section-index', 'MasterController@sectionIndex')->name('admin.Master.section.index');	   
	    Route::post('section-store/{id?}', 'MasterController@sectionStore')->name('admin.Master.section.store');   
	    Route::get('section-edit/{id}', 'MasterController@sectionEdit')->name('admin.Master.section.edit');	   
	    Route::get('section-delete/{id}', 'MasterController@sectionDelete')->name('admin.Master.section.delete');
	    //subject
	    Route::get('subject-index', 'MasterController@subjectIndex')->name('admin.Master.subject.index');	   
	    Route::post('subject-store/{id?}', 'MasterController@subjectStore')->name('admin.Master.subject.store');   
	    Route::get('subject-edit/{id}', 'MasterController@subjectEdit')->name('admin.Master.subject.edit');	   
	    Route::get('subject-delete/{id}', 'MasterController@subjectDelete')->name('admin.Master.subject.delete');
	    //questiontype
	    Route::get('questiontype-index', 'MasterController@questiontypeIndex')->name('admin.Master.questiontype.index');	   
	    Route::post('questiontype-store/{id?}', 'MasterController@questiontypeStore')->name('admin.Master.questiontype.store');   
	    Route::get('questiontype-edit/{id}', 'MasterController@questiontypeEdit')->name('admin.Master.questiontype.edit');	   
	    Route::get('questiontype-delete/{id}', 'MasterController@questiontypeDelete')->name('admin.Master.questiontype.delete');
	    //difficultylevel
	    Route::get('difficultylevel-index', 'MasterController@difficultylevelIndex')->name('admin.Master.difficultylevel.index');	   
	    Route::post('difficultylevel-store/{id?}', 'MasterController@difficultylevelStore')->name('admin.Master.difficultylevel.store');   
	    Route::get('difficultylevel-edit/{id}', 'MasterController@difficultylevelEdit')->name('admin.Master.difficultylevel.edit');	   
	    Route::get('difficultylevel-delete/{id}', 'MasterController@difficultylevelDelete')->name('admin.Master.difficultylevel.delete');
	    //subjectsection
	    Route::get('subjectsection-index', 'MasterController@subjectsectionIndex')->name('admin.Master.subjectsection.index');	   
	    Route::post('subjectsection-store', 'MasterController@subjectsectionStore')->name('admin.Master.subjectsection.store');   
	    Route::get('subjectsection-table', 'MasterController@subjectsectionTable')->name('admin.Master.subjectsection.table');   
	    Route::get('subjectsection-delete/{id}', 'MasterController@subjectsectionDelete')->name('admin.Master.subjectsection.delete');
	    //topic
	    Route::get('topic-index', 'MasterController@topicIndex')->name('admin.Master.topic.index');	   
	    Route::post('topic-store', 'MasterController@topicStore')->name('admin.Master.topic.store');   
	    Route::get('topic-table', 'MasterController@topicTable')->name('admin.Master.topic.table');   
	    Route::get('topic-delete/{id}', 'MasterController@topicDelete')->name('admin.Master.topic.delete');
	});
    Route::group(['prefix' => 'question'], function() {
    	Route::get('index','QuestionController@index')->name('admin.question.index');
    	Route::get('option','QuestionController@option')->name('admin.question.option');
    	Route::post('store','QuestionController@store')->name('admin.question.store');

    	Route::get('add','Exam\QuestionController@questionForm')->name('admin.question.add');
    	Route::post('store/{id?}/{question_description_id?}','Exam\QuestionController@questionStore')->name('admin.question.store');
    	Route::get('edit/{id}','Exam\QuestionController@questionEdit')->name('admin.question.edit');
    	Route::post('edit-show-form','Exam\QuestionController@questionEditShow')->name('admin.question.edit.show');
    	Route::get('show','Exam\QuestionController@show')->name('admin.question.show');
    	Route::post('show-table','Exam\QuestionController@showTable')->name('admin.question.show.table');
    	Route::post('show-print','Exam\QuestionController@showPrint')->name('admin.question.show.print');
    	Route::post('draft-store','Exam\QuestionController@questionDraftStore')->name('admin.question.draft.store');

    	Route::get('paragraph','Exam\QuestionController@paragraph')->name('admin.paragraph.list');  
    	Route::post('paragraph-store','Exam\QuestionController@paragraphStore')->name('admin.paragraph.store'); 
    	Route::get('paragraph-select','Exam\QuestionController@paragraphSelect')->name('admin.paragraph.select');  


    	Route::get('verify','Exam\QuestionController@questionForm')->name('admin.question.verify');  
    	Route::post('verify-store/{id?}','Exam\QuestionController@questionVerifyStore')->name('admin.question.verify.store');  
    });



 });

