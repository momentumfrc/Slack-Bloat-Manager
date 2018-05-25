package org.usfirst.frc.team4999.gui;

import javax.swing.JPanel;

public class ModeSwitcher extends JPanel {
	
	private JPanel selected;
	
	private ChannelsComponent channels;
	private FilesComponent files;
	
	public ModeSwitcher() {
		channels = new ChannelsComponent();
		files = new FilesComponent();
		selected = files;
	}
	
	public JPanel getSelected() {
		return selected;
	}

}
