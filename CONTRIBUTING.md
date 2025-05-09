# Contributing

Contributions are welcome and will be fully credited.

## Things you can do

- Report bugs
- Fix bugs
- Add new features
- Add new tests
- Add documentation

## Pull Requests

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the README.md and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow SemVer. Randomly breaking public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

## Running Tests

Before submitting a pull request, please ensure all tests pass:

1. Set up your Firebase project and obtain the credentials JSON file
2. Configure your `.env` file with the required Firebase variable:
```env
FIREBASE_PROJECT_ID=your-project-id
```

3. Place your Firebase service account credentials JSON file in your Laravel storage directory:
```
/storage/firebase.json
```

4. Run the tests:
```bash
composer test
```

## Security

If you discover any security related issues, please email devkandil@gmail.com instead of using the issue tracker.

## Credits

Thank you to all the people who have already contributed to Laravel FCM!