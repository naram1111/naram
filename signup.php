# views.py
from django.shortcuts import render, redirect
from django.contrib import messages
from django.contrib.auth.models import User

def register_view(request):
    if request.method == 'POST':
        phone_number = request.POST.get('phone_number')
        password = request.POST.get('password')

        if not phone_number or not password:
            return render(request, 'error.html', {'message': 'Please fill in all required fields.'})

        if User.objects.filter(username=phone_number).exists():
            return render(request, 'error.html', {'message': 'Phone number already registered. Please use a different phone number.'})

        user = User.objects.create_user(username=phone_number, password=password)
        if user:
            return render(request, 'success.html', {'message': 'Signup successful!'})
        else:
            return render(request, 'error.html', {'message': 'Error: Signup failed. Please try again later.'})
    else:
        return render(request, 'error.html', {'message': 'Invalid request method.'})
