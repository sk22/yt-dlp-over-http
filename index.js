const { createServer } = require("http");
const { spawn } = require("child_process");
const { URLSearchParams, URL } = require("url");

const server = createServer((req, res) => {
	const params = new URLSearchParams(req.url.slice(req.url.indexOf("?")));
	const url = params.get("url");
	const args = ["-o", "-", url];
	console.info("spawning yt-dlp", ...args);
	const child = spawn("yt-dlp", args);
	child.stdout.pipe(res);
	child.stderr.pipe(process.stderr);
	req.on("close", () => {
		console.info("sending sigint to yt-dlp for url", url);
		child.kill("SIGINT");
	});
});

const port = process.env.PORT || 3000;
const host = process.env.HOST || "127.0.0.1";
server.listen(port, host);
console.info(`listening on http://${host}:${port}`);
