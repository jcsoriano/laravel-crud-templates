# Security Policy

Thank you for taking the time to responsibly disclose a security vulnerability. We greatly appreciate community efforts to help improve the security of this package.

---

## Supported Versions

Only the most recent stable release will receive security fixes.

| Version                         | Supported |
| ------------------------------- | --------- |
| ✅ Latest (e.g., `^1.0`)         | Yes       |
| ❌ Older / unmaintained releases | No        |

If you are using an outdated version, please upgrade before submitting a report.

---

## Reporting a Vulnerability

If you discover a security-related issue, **please do not report it publicly** through GitHub issues or discussions.

Instead, email:

```
hi@jcsoriano.com
```

Include:

* A clear description of the issue
* Steps to reproduce
* Potential impact and severity
* Any proof-of-concept code (if applicable)
* Your environment (PHP/Laravel versions)

We will respond as quickly as possible.

---

## Expected Disclosure Process

1. You report the vulnerability privately via email.
2. We acknowledge receipt within **72 hours** (typically sooner).
3. We investigate, confirm, and work on a fix.
4. We issue a patched release.
5. We publish release notes describing the fix **without revealing exploit details** until users have time to update.

We may request additional information to reproduce or verify the issue.

---

## Public Disclosure

Please do **not**:

* File a public GitHub issue
* Tweet or blog about the vulnerability
* Share it with third parties

Until a patch has been released.

Coordinated disclosure helps protect all users.

---

## Scope

We are primarily interested in:

* Data leakage
* Unauthorized access or privilege escalation
* Remote code execution
* Significant denial-of-service vectors
* Input validation / sanitization weaknesses

Out of scope:

* Issues requiring root/system access
* Social engineering attacks
* Weaknesses requiring modification of vendor libraries
* Deprecated package versions

---

## Responsible Researcher Recognition

Contributors who follow this process:

* May be credited in changelogs or release notes (optional)
* May request anonymous acknowledgement

Thank you for keeping the ecosystem safe.

---

## Dependencies

If the vulnerability originates from an external dependency, we may forward the report to that project’s maintainers.

---

## License of Contributions

Security patches are contributed under the package’s existing license.

---

### Thank You

We appreciate your effort to improve the security and stability of this package! 🙏
