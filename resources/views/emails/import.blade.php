<div class="mail_container">
	<p>Hi {{$user->first_name}},</p>
	<p>Your import is complete. The results are listed below:</p>

    <h3>Classes Sheet Errors</h3>
    @foreach ($errors['classes'] as $class_error)
        <p>{{$class_error}}</p>
    @endforeach

    <h3>Teachers Sheet Errors</h3>
    @foreach ($errors['teachers'] as $teacher_error)
        <p>{{$teacher_error}}</p>
    @endforeach

    <h3>Students Sheet Errors</h3>
    @foreach ($errors['students'] as $student_error)
        <p>{{$student_error}}</p>
    @endforeach

	<p>Thank you.</p>
</div>
