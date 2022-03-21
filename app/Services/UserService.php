<?php

namespace App\Services;

use App\Models\User;
use Awurth\SlimValidation\Validator;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as V;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Класс-сервис для работы с пользователями системы.
 */
class UserService
{
    /**
     * @var Validator Объект для валидации запросов.
     */
    protected Validator $validator;

    /**
     * Инициализация сервиса.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->validator = $container->get('validator');
    }

    /**
     * Регистрация нового пользователя.
     * @param array $data
     * 
     * @throws \Respect\Validation\Exceptions\ValidationException
     * 
     * @return User
     */
    public function registerNewUser(array $data) : User
    {
        // валидируем переданные данные
        $this->validator->validate($data, [
            'name' => V::notEmpty()->regex('/^[a-zA-Z\d]+$/')->length(3, 255),
            'password' => V::notEmpty()->length(6, 255),
            'confirm_password' => V::equals($data['password']),
        ], null, [
            'notEmpty' => 'обязательно к заполнению', 
            'equals' => 'пароли должны совпадать',
            'length' => 'допустимое кол-во символов от {{minValue}} до {{maxValue}}',
            'regex' => 'может содержать только латинские буквы или цифры'
        ]);

        // проверяем уникальность имени пользователя
        if (User::whereName($data['name'])->exists()) {
            $this->validator->addError('name', 'Такой пользователь уже существует');
        }

        if (!$this->validator->isValid()) {
            throw new ValidationException('Неверные данные', 422);
        }

        // если валидация прошла успешно - создаем нового юзера в базе данных
        $user = new User();
        $user->name = $data['name'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();

        return $user;
    }
}