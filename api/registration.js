export default async function handler(req, res) {
    if (req.method === 'POST') {
        const { name, email } = req.body;
        // Add logic to handle form data here
        res.status(200).json({ message: 'Registration successful!' });
    } else {
        res.status(405).json({ message: 'Method not allowed' });
    }
}
